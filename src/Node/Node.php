<?php

namespace DAMA\MenuBundle\Node;

use Symfony\Component\ExpressionLanguage\Expression;

class Node
{
    /**
     * @var int
     */
    private static $counter = 0;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var NodeFactoryInterface|null
     */
    protected $nodeFactory;

    /**
     * @var string|null
     */
    protected $label;

    /**
     * @var string|null
     */
    protected $url;

    /**
     * @var string|null
     */
    protected $route;

    /**
     * @var array<string, mixed>
     */
    protected $routeParams = [];

    /**
     * @var array<string>
     */
    protected $additionalActiveRoutes = [];

    /**
     * @var array<string|Expression|mixed>
     */
    protected $requiredPermissions = [];

    /**
     * @var array<mixed, mixed>
     */
    protected $attr = [];

    /**
     * @var Node|null
     */
    protected $parent;

    /**
     * @var array<self>
     */
    protected $children = [];

    /**
     * @var bool
     */
    protected $active = false;

    /**
     * @var bool|null
     */
    protected $ifTrue = null;

    /**
     * @var bool
     */
    protected $removeIfNoChildren = false;

    public function __construct(?string $label = null)
    {
        $this->label = $label;
        $this->id = self::$counter++;
    }

    /**
     * @return $this
     */
    public function setParent(Node $parent = null): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    /**
     * @return $this
     */
    public function addChild(Node $child): self
    {
        $this->children[$child->getId()] = $child;
        $child->setParent($this);

        return $this;
    }

    public function ifTrue(bool $condition): self
    {
        $this->ifTrue = $condition;

        return $this;
    }

    public function endIf(): self
    {
        if ($this->ifTrue === null) {
            throw new \LogicException('Not currently inside an open ifTrue block. So cannot end it.');
        }

        $this->ifTrue = null;

        return $this;
    }

    /**
     * @throws \BadMethodCallException
     */
    public function child(?string $label = null): self
    {
        if (!$this->nodeFactory) {
            throw new \BadMethodCallException('nodeFactory needs to be set on this node to be able
                to use shortcut ->child() for adding a child node');
        }

        $child = $this->nodeFactory->create($label);

        if ($this->ifTrue === null || $this->ifTrue) {
            $this->addChild($child);
        } else {
            $child->setParent($this);
        }

        return $child;
    }

    public function end(): ?self
    {
        return $this->parent;
    }

    public function removeChild(Node $child): void
    {
        if (isset($this->children[$child->getId()])) {
            unset($this->children[$child->getId()]);
            $child->setParent();
        }
    }

    /**
     * @return array<self>
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * returns the layer of this node in the menu tree.
     * Root node has layer 0. So for actual menu nodes the layer starts with 1.
     */
    public function getLayer(): int
    {
        if ($this->parent) {
            return $this->parent->getLayer() + 1;
        }

        return 0; //root
    }

    /**
     * @return $this
     */
    public function setActive(bool $active): self
    {
        $this->active = $active;

        if ($this->parent && $active) {
            $this->parent->setActive(true);
        }

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * returns the first active child.
     */
    public function getFirstActiveChild(): ?self
    {
        if ($this->active) {
            foreach ($this->children as $child) {
                if ($child->isActive()) {
                    return $child;
                }
            }
        }

        return null;
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param array<string> $additionalActiveRoutes
     *
     * @return $this
     */
    public function setAdditionalActiveRoutes(array $additionalActiveRoutes): self
    {
        $this->additionalActiveRoutes = $additionalActiveRoutes;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getAdditionalActiveRoutes(): array
    {
        return $this->additionalActiveRoutes;
    }

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function setAttr(string $key, $value): self
    {
        $this->attr[$key] = $value;

        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getAttr(string $key)
    {
        if (isset($this->attr[$key])) {
            return $this->attr[$key];
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    public function getAttrs(): array
    {
        return $this->attr;
    }

    /**
     * @return $this
     */
    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @param array<string|Expression|mixed> $requiredPermissions
     *
     * @return $this
     */
    public function setRequiredPermissions(array $requiredPermissions): self
    {
        $this->requiredPermissions = $requiredPermissions;

        return $this;
    }

    /**
     * @return array<string|Expression|mixed>
     */
    public function getRequiredPermissions(): array
    {
        return $this->requiredPermissions;
    }

    /**
     * @return $this
     *
     * @throws \LogicException
     */
    public function setRoute(?string $route): self
    {
        if ($this->url != null && $route !== null) {
            throw new \LogicException('You can either set a url OR a route, but not both!');
        }

        $this->route = $route;

        return $this;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    /**
     * @param array<string, mixed> $routeParams
     *
     * @return $this
     */
    public function setRouteParams(array $routeParams): self
    {
        $this->routeParams = $routeParams;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getRouteParams(): array
    {
        return $this->routeParams;
    }

    public function isRootNode(): bool
    {
        return $this->parent === null;
    }

    public function hasRoute(): bool
    {
        return $this->getRoute() !== null;
    }

    /**
     * @return array<string>
     */
    public function getAllActiveRoutes(): array
    {
        return array_filter(array_merge([$this->getRoute()], $this->getAdditionalActiveRoutes()));
    }

    public function isFirstChild(): bool
    {
        if (!$this->parent) {
            return false;
        }

        $children = $this->parent->getChildren();

        return $this === reset($children);
    }

    /**
     * returns first child node with route.
     */
    public function getFirstChildWithRoute(): ?self
    {
        foreach ($this->children as $child) {
            if ($child->hasRoute()) {
                return $child;
            }
        }

        return null;
    }

    public function setNodeFactory(NodeFactoryInterface $nodeFactory): void
    {
        $this->nodeFactory = $nodeFactory;
    }

    public function getNodeFactory(): ?NodeFactoryInterface
    {
        return $this->nodeFactory;
    }

    /**
     * @return $this
     *
     * @throws \LogicException
     */
    public function setUrl(?string $url): self
    {
        if ($this->route != null && $url !== null) {
            throw new \LogicException('You can either set a url OR a route, but not both!');
        }

        $this->url = $url;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function hasUrl(): bool
    {
        return $this->url !== null;
    }

    public function isRemoveIfNoChildren(): bool
    {
        return $this->removeIfNoChildren;
    }

    /**
     * @return $this
     */
    public function setRemoveIfNoChildren(bool $removeIfNoChildren): self
    {
        $this->removeIfNoChildren = $removeIfNoChildren;

        return $this;
    }

    public static function resetCounter(): void
    {
        self::$counter = 0;
    }
}
