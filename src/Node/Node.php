<?php

namespace DAMA\MenuBundle\Node;

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
     * @var NodeFactoryInterface
     */
    protected $nodeFactory;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $route;

    /**
     * @var array
     */
    protected $routeParams = [];

    /**
     * @var array
     */
    protected $additionalActiveRoutes = [];

    /**
     * @var array
     */
    protected $requiredPermissions = [];

    /**
     * @var array
     */
    protected $attr = [];

    /**
     * @var Node
     */
    protected $parent;

    /**
     * @var array
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

    /**
     * @param null $label
     */
    public function __construct($label = null)
    {
        $this->label = $label;
        $this->id = self::$counter++;
    }

    /**
     * @param Node $parent
     *
     * @return $this
     */
    public function setParent(Node $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Node
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return $this
     */
    public function addChild(Node $child)
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
     * @param null $label
     *
     * @return Node
     *
     * @throws \BadMethodCallException
     */
    public function child($label = null)
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

    /**
     * @return Node
     */
    public function end()
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
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * returns the layer of this node in the menu tree.
     * Root node has layer 0. So for actual menu nodes the layer starts with 1.
     *
     * @return int
     */
    public function getLayer()
    {
        if ($this->parent) {
            return $this->parent->getLayer() + 1;
        }

        return 0; //root
    }

    /**
     * @param $active
     *
     * @return $this
     */
    public function setActive($active)
    {
        $this->active = $active;

        if ($this->parent && $active) {
            $this->parent->setActive(true);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * returns the first active child.
     *
     * @return Node|null
     */
    public function getFirstActiveChild()
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

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return $this
     */
    public function setAdditionalActiveRoutes(array $additionalActiveRoutes)
    {
        $this->additionalActiveRoutes = $additionalActiveRoutes;

        return $this;
    }

    /**
     * @return array
     */
    public function getAdditionalActiveRoutes()
    {
        return $this->additionalActiveRoutes;
    }

    /**
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function setAttr($key, $value)
    {
        $this->attr[$key] = $value;

        return $this;
    }

    /**
     * @param $key
     */
    public function getAttr($key)
    {
        if (isset($this->attr[$key])) {
            return $this->attr[$key];
        }

        return null;
    }

    /**
     * @return array
     */
    public function getAttrs()
    {
        return $this->attr;
    }

    /**
     * @param $label
     *
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return $this
     */
    public function setRequiredPermissions(array $requiredPermissions)
    {
        $this->requiredPermissions = $requiredPermissions;

        return $this;
    }

    /**
     * @return array
     */
    public function getRequiredPermissions()
    {
        return $this->requiredPermissions;
    }

    /**
     * @param $route
     *
     * @return $this
     *
     * @throws \LogicException
     */
    public function setRoute($route)
    {
        if ($this->url != null) {
            throw new \LogicException('You can either set a url OR a route, but not both!');
        }

        $this->route = $route;

        return $this;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @return $this
     */
    public function setRouteParams(array $routeParams)
    {
        $this->routeParams = $routeParams;

        return $this;
    }

    /**
     * @return array
     */
    public function getRouteParams()
    {
        return $this->routeParams;
    }

    /**
     * @return bool
     */
    public function isRootNode()
    {
        return $this->parent === null;
    }

    /**
     * @return bool
     */
    public function hasRoute()
    {
        return $this->getRoute() !== null;
    }

    /**
     * @return array
     */
    public function getAllActiveRoutes()
    {
        return array_merge([$this->getRoute()], $this->getAdditionalActiveRoutes());
    }

    /**
     * @return bool
     */
    public function isFirstChild()
    {
        if (!$this->parent) {
            return false;
        }

        $children = $this->parent->getChildren();

        return $this === reset($children);
    }

    /**
     * returns first child node with route.
     *
     * @return Node|null
     */
    public function getFirstChildWithRoute()
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

    /**
     * @return NodeFactoryInterface
     */
    public function getNodeFactory()
    {
        return $this->nodeFactory;
    }

    /**
     * @param $url
     *
     * @return $this
     *
     * @throws \LogicException
     */
    public function setUrl($url)
    {
        if ($this->route != null) {
            throw new \LogicException('You can either set a url OR a route, but not both!');
        }

        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return bool
     */
    public function hasUrl()
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
}
