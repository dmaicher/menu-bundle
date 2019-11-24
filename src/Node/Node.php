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
    protected $routeParams = array();

    /**
     * @var array
     */
    protected $additionalActiveRoutes = array();

    /**
     * @var array
     */
    protected $requiredPermissions = array();

    /**
     * @var array
     */
    protected $attr = array();

    /**
     * @var Node
     */
    protected $parent;

    /**
     * @var array
     */
    protected $children = array();

    /**
     * @var bool
     */
    protected $active = false;

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
     * @param Node $child
     *
     * @return $this
     */
    public function addChild(Node $child)
    {
        $this->children[$child->getId()] = $child;
        $child->setParent($this);

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
        $this->addChild($child);

        return $child;
    }

    /**
     * @return Node
     */
    public function end()
    {
        return $this->parent;
    }

    /**
     * @param Node $child
     */
    public function removeChild(Node $child)
    {
        if (isset($this->children[$child->getId()])) {
            unset($this->children[$child->getId()]);
            $child->setParent(null);
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

        return;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param array $additionalActiveRoutes
     *
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

        return  $this;
    }

    /**
     * @param $key
     */
    public function getAttr($key)
    {
        if (isset($this->attr[$key])) {
            return $this->attr[$key];
        }
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
     * @param array $requiredPermissions
     *
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
     * @param array $routeParams
     *
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
        return array_merge(array($this->getRoute()), $this->getAdditionalActiveRoutes());
    }

    /**
     * @return bool
     */
    public function isFirstChild()
    {
        if ($this->parent) {
            $children = $this->parent->getChildren();

            return $this === reset($children);
        }

        return false;
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

        return;
    }

    /**
     * @param \DAMA\MenuBundle\Node\NodeFactoryInterface $nodeFactory
     */
    public function setNodeFactory(NodeFactoryInterface $nodeFactory)
    {
        $this->nodeFactory = $nodeFactory;
    }

    /**
     * @return \DAMA\MenuBundle\Node\NodeFactoryInterface
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
}
