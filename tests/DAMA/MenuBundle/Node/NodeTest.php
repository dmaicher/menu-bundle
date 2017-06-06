<?php

namespace DAMA\MenuBundle\Tests\Node;

use DAMA\MenuBundle\Node\Node;

class NodeTest extends \PHPUnit_Framework_TestCase
{
    public function testAddChild()
    {
        $node = new Node();
        $child = new Node();
        $node->addChild($child);

        $this->assertCount(1, $node->getChildren());
        $this->assertSame($node, $child->getParent());
    }

    public function testRemoveChild()
    {
        $node = new Node();
        $child = new Node();
        $node->addChild($child);

        $node->removeChild($child);

        $this->assertEmpty($node->getChildren());
        $this->assertNull($child->getParent());
    }

    public function testIsFirstChild()
    {
        $node = new Node();
        $child1 = new Node();
        $child2 = new Node();
        $node->addChild($child1);
        $node->addChild($child2);

        $this->assertSame(true, $child1->isFirstChild());
        $this->assertSame(false, $child2->isFirstChild());
    }

    public function testGetLayerReturnsZero()
    {
        $node = new Node();
        $this->assertSame(0, $node->getLayer());
    }

    public function testGetLayerReturnsOne()
    {
        $node = new Node();
        $child = new Node();
        $node->addChild($child);

        $this->assertSame(1, $child->getLayer());
    }

    public function testGetLayerReturnsTwo()
    {
        $node = new Node();
        $child = new Node();
        $childChild = new Node();
        $child->addChild($childChild);
        $node->addChild($child);

        $this->assertSame(2, $childChild->getLayer());
    }

    public function testSetActive()
    {
        $node = new Node();
        $child = new Node();
        $node->addChild($child);

        $child->setActive(true);

        $this->assertSame(true, $child->isActive());
        $this->assertSame(true, $node->isActive());
    }

    public function testGetFirstActiveChildReturnsNull()
    {
        $node = new Node();

        $this->assertNull($node->getFirstActiveChild());
    }

    public function testGetFirstActiveChildReturnsSecondChild()
    {
        $node = new Node();
        $child1 = new Node();
        $child2 = new Node();
        $node->addChild($child1);
        $node->addChild($child2);

        $child2->setActive(true);

        $this->assertSame($child2, $node->getFirstActiveChild());
    }

    public function testGetAllActiveRoutes()
    {
        $node = new Node();
        $node->setRoute('some_route');
        $node->setAdditionalActiveRoutes(array('some_other_route'));

        $this->assertEquals(array('some_route', 'some_other_route'), $node->getAllActiveRoutes());
    }

    public function testGetFirstChildWithRouteReturnsNull()
    {
        $node = new Node();

        $this->assertNull($node->getFirstChildWithRoute());
    }

    public function testGetFirstChildWithRouteReturnsSecondChild()
    {
        $node = new Node();
        $child1 = new Node();
        $child2 = new Node();
        $child2->setRoute('some_route');
        $node->addChild($child1);
        $node->addChild($child2);

        $this->assertSame($child2, $node->getFirstChildWithRoute());
    }
}
