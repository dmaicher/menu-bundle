<?php

namespace Tests\Menu;

use DAMA\MenuBundle\Menu\MenuFactory;
use DAMA\MenuBundle\MenuConfig\MenuConfigProvider;
use DAMA\MenuBundle\MenuTree\MenuTreeBuilderInterface;
use DAMA\MenuBundle\MenuTree\MenuTreeTraverserInterface;
use DAMA\MenuBundle\Node\Node;
use DAMA\MenuBundle\Node\NodeFactoryInterface;
use PHPUnit\Framework\TestCase;

class MenuFactoryTest extends TestCase
{
    protected $nodeFactory;

    protected $treeBuilder;

    protected $menuConfigProvider;

    protected $menuTreeTraverser;

    /**
     * @var MenuFactory
     */
    protected $menuFactory;

    public function setUp(): void
    {
        $this->nodeFactory = $this->createMock(NodeFactoryInterface::class);
        $this->treeBuilder = $this->createMock(MenuTreeBuilderInterface::class);
        $this->menuConfigProvider = $this->createMock(MenuConfigProvider::class);
        $this->menuTreeTraverser = $this->createMock(MenuTreeTraverserInterface::class);

        $this->menuFactory = new MenuFactory($this->menuConfigProvider, $this->menuTreeTraverser);
    }

    public function testCreate()
    {
        $node = $this->getMockBuilder(Node::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->nodeFactory
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($node))
        ;

        $this->menuConfigProvider
            ->expects($this->once())
            ->method('getMenuConfig')
            ->with('name')
            ->will($this->returnValue(array(
                'tree_builder' => $this->treeBuilder,
                'node_factory' => $this->nodeFactory,
            )))
        ;

        $this->menuTreeTraverser
            ->expects($this->once())
            ->method('traverse')
            ->with($node)
        ;

        $this->treeBuilder
            ->expects($this->once())
            ->method('build')
            ->with($node)
        ;

        $this->menuFactory->create('name');
    }
}
