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
    protected $cache = [];

    public function __construct(MenuConfigProvider $menuConfigProvider, MenuTreeTraverserInterface $menuTreeTraverser)
    {
        $this->menuConfigProvider = $menuConfigProvider;
        $this->menuTreeTraverser = $menuTreeTraverser;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function create(string $name): Node
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

    protected function getRootNode(NodeFactoryInterface $nodeFactory, MenuTreeBuilderInterface $menuTreeBuilder): Node
    {
        $root = $nodeFactory->create(null);
        $menuTreeBuilder->build($root);

        return $root;
    }
}
