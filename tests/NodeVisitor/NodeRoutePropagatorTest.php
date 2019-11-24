<?php

namespace Tests\NodeVisitor;

use DAMA\MenuBundle\Node\Node;
use DAMA\MenuBundle\NodeVisitor\NodeActivator;
use DAMA\MenuBundle\NodeVisitor\NodeRoutePropagator;
use PHPUnit\Framework\TestCase;

class NodeRoutePropagatorTest extends TestCase
{
    /**
     * @var NodeActivator
     */
    protected $propagator;

    /**
     * @var Node
     */
    protected $node;

    /**
     * @var Node
     */
    protected $parent;

    public function setUp(): void
    {
        $this->propagator = new NodeRoutePropagator();
        $this->node = new Node();
        $this->parent = new Node();
        $this->parent->addChild($this->node);
    }

    /**
     * @dataProvider getTestData
     */
    public function testVisit($childRoute, $parentRoute, $expectedParentRoute)
    {
        $this->node->setRoute($childRoute);
        $this->parent->setRoute($parentRoute);

        $this->propagator->visit($this->node);

        $this->assertEquals($expectedParentRoute, $this->parent->getRoute());
    }

    public function getTestData()
    {
        return array(
            array(null, 'some_route', 'some_route'),
            array('some_route', null, 'some_route'),
            array('some_route', 'some_other_route', 'some_other_route'),
        );
    }
}
