<?php

namespace Tests\Node;

use DAMA\MenuBundle\Node\Node;
use DAMA\MenuBundle\Node\NodeFactory;
use PHPUnit\Framework\TestCase;

class NodeTest extends TestCase
{
    public function testAddChild(): void
    {
        $node = new Node();
        $child = new Node();
        $node->addChild($child);

        $this->assertCount(1, $node->getChildren());
        $this->assertSame($node, $child->getParent());
    }

    public function testRemoveChild(): void
    {
        $node = new Node();
        $child = new Node();
        $node->addChild($child);

        $node->removeChild($child);

        $this->assertEmpty($node->getChildren());
        $this->assertNull($child->getParent());
    }

    public function testIsFirstChild(): void
    {
        $node = new Node();
        $child1 = new Node();
        $child2 = new Node();
        $node->addChild($child1);
        $node->addChild($child2);

        $this->assertSame(true, $child1->isFirstChild());
        $this->assertSame(false, $child2->isFirstChild());
    }

    public function testGetLayerReturnsZero(): void
    {
        $node = new Node();
        $this->assertSame(0, $node->getLayer());
    }

    public function testGetLayerReturnsOne(): void
    {
        $node = new Node();
        $child = new Node();
        $node->addChild($child);

        $this->assertSame(1, $child->getLayer());
    }

    public function testGetLayerReturnsTwo(): void
    {
        $node = new Node();
        $child = new Node();
        $childChild = new Node();
        $child->addChild($childChild);
        $node->addChild($child);

        $this->assertSame(2, $childChild->getLayer());
    }

    public function testSetActive(): void
    {
        $node = new Node();
        $child = new Node();
        $node->addChild($child);

        $child->setActive(true);

        $this->assertSame(true, $child->isActive());
        $this->assertSame(true, $node->isActive());
    }

    public function testGetFirstActiveChildReturnsNull(): void
    {
        $node = new Node();

        $this->assertNull($node->getFirstActiveChild());
    }

    public function testGetFirstActiveChildReturnsSecondChild(): void
    {
        $node = new Node();
        $child1 = new Node();
        $child2 = new Node();
        $node->addChild($child1);
        $node->addChild($child2);

        $child2->setActive(true);

        $this->assertSame($child2, $node->getFirstActiveChild());
    }

    public function testGetAllActiveRoutes(): void
    {
        $node = new Node();
        $node->setRoute('some_route');
        $node->setAdditionalActiveRoutes(['some_other_route']);

        $this->assertEquals(['some_route', 'some_other_route'], $node->getAllActiveRoutes());
    }

    public function testGetFirstChildWithRouteReturnsNull(): void
    {
        $node = new Node();

        $this->assertNull($node->getFirstChildWithRoute());
    }

    public function testGetFirstChildWithRouteReturnsSecondChild(): void
    {
        $node = new Node();
        $child1 = new Node();
        $child2 = new Node();
        $child2->setRoute('some_route');
        $node->addChild($child1);
        $node->addChild($child2);

        $this->assertSame($child2, $node->getFirstChildWithRoute());
    }

    /**
     * @testWith [true]
     *           [false]
     */
    public function testAddConditionalChildNode(bool $condition): void
    {
        $node = (new NodeFactory())->create('foo');
        $node
            ->ifTrue($condition)
                ->child('bar')
                    ->child('baz')
                    ->end()
                ->end()
                ->child('foobar')
                ->end()
            ->endIf()
            ->child('foobaz')
            ->end()
        ;

        $this->assertCount($condition ? 3 : 1, $node->getChildren());
    }
}
