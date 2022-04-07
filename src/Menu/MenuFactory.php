<?php

namespace DAMA\MenuBundle\Menu;

use DAMA\MenuBundle\MenuConfig\MenuConfigProvider;
use DAMA\MenuBundle\MenuTree\MenuTreeBuilderInterface;
use DAMA\MenuBundle\MenuTree\MenuTreeTraverserInterface;
use DAMA\MenuBundle\Node\Node;
use DAMA\MenuBundle\Node\NodeFactoryInterface;

final class MenuFactory implements MenuFactoryInterface
{
    /**
     * @var MenuConfigProvider
     */
    private $menuConfigProvider;

    /**
     * @var MenuTreeTraverserInterface
     */
    private $menuTreeTraverser;

    /**
     * @var array<string, Node>
     */
    private $cache = [];

    public function __construct(MenuConfigProvider $menuConfigProvider, MenuTreeTraverserInterface $menuTreeTraverser)
    {
        $this->menuConfigProvider = $menuConfigProvider;
        $this->menuTreeTraverser = $menuTreeTraverser;
    }

    public function create(string $name): Node
    {
        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        $menuConfig = $this->menuConfigProvider->getMenuConfig($name);
        /** @var NodeFactoryInterface $nodeFactory */
        $nodeFactory = $menuConfig['node_factory'];
        /** @var MenuTreeBuilderInterface $treeBuilder */
        $treeBuilder = $menuConfig['tree_builder'];

        $root = $this->getRootNode($nodeFactory, $treeBuilder);

        $this->menuTreeTraverser->traverse($root);

        return $this->cache[$name] = $root;
    }

    private function getRootNode(NodeFactoryInterface $nodeFactory, MenuTreeBuilderInterface $menuTreeBuilder): Node
    {
        $root = $nodeFactory->create(null);
        $menuTreeBuilder->build($root);

        return $root;
    }
}
