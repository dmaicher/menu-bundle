<?php

namespace DAMA\MenuBundle\Menu;

use DAMA\MenuBundle\MenuConfig\MenuConfigProvider;
use DAMA\MenuBundle\MenuTree\MenuTreeBuilderInterface;
use DAMA\MenuBundle\MenuTree\MenuTreeTraverserInterface;
use DAMA\MenuBundle\Node\Node;
use DAMA\MenuBundle\Node\NodeFactoryInterface;

class MenuFactory implements MenuFactoryInterface
{
    /**
     * @var MenuConfigProvider
     */
    protected $menuConfigProvider;

    /**
     * @var MenuTreeTraverserInterface
     */
    protected $menuTreeTraverser;

    /**
     * @var array
     */
    protected $cache = array();

    /**
     * @param MenuConfigProvider         $menuConfigProvider
     * @param MenuTreeTraverserInterface $menuTreeTraverser
     */
    public function __construct(MenuConfigProvider $menuConfigProvider, MenuTreeTraverserInterface $menuTreeTraverser)
    {
        $this->menuConfigProvider = $menuConfigProvider;
        $this->menuTreeTraverser = $menuTreeTraverser;
    }

    /**
     * @param $name
     *
     * @return Node
     *
     * @throws \InvalidArgumentException
     */
    public function create($name)
    {
        //already created for this request?
        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        $menuConfig = $this->menuConfigProvider->getMenuConfig($name);

        $root = $this->getRootNode($menuConfig['node_factory'], $menuConfig['tree_builder']);

        $this->menuTreeTraverser->traverse($root);

        //store in "cache"
        $this->cache[$name] = $root;

        return $root;
    }

    /**
     * @param NodeFactoryInterface     $nodeFactory
     * @param MenuTreeBuilderInterface $menuTreeBuilder
     *
     * @return Node
     */
    protected function getRootNode(NodeFactoryInterface $nodeFactory, MenuTreeBuilderInterface $menuTreeBuilder)
    {
        $root = $nodeFactory->create(null);
        $menuTreeBuilder->build($root);

        return $root;
    }
}
