<?php

declare(strict_types=1);

namespace Tests\NodeVisitor;

use DAMA\MenuBundle\Node\Node;
use DAMA\MenuBundle\NodeVisitor\NodeRoutePropagator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NodeRoutePropagatorTest extends TestCase
{
    private NodeRoutePropagator $propagator;
    private Node $node;
    private Node $parent;

    public function setUp(): void
    {
        $this->propagator = new NodeRoutePropagator();
        $this->node = new Node();
        $this->parent = new Node();
        $this->parent->addChild($this->node);
    }

    #[DataProvider('getTestData')]
    public function testVisit($childRoute, $parentRoute, $expectedParentRoute): void
    {
        $this->node->setRoute($childRoute);
        $this->parent->setRoute($parentRoute);

        $this->propagator->visit($this->node);

        $this->assertEquals($expectedParentRoute, $this->parent->getRoute());
    }

    public static function getTestData(): array
    {
        return [
            [null, 'some_route', 'some_route'],
            ['some_route', null, 'some_route'],
            ['some_route', 'some_other_route', 'some_other_route'],
        ];
    }
}
