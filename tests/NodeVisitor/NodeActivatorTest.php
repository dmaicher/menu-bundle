<?php

namespace Tests\NodeVisitor;

use DAMA\MenuBundle\Node\Node;
use DAMA\MenuBundle\NodeVisitor\NodeActivator;
use PHPUnit\Framework\TestCase;

class NodeActivatorTest extends TestCase
{
    /**
     * @var Node
     */
    protected $node;

    public function setUp(): void
    {
        $this->node = new Node();
    }

    /**
     * @dataProvider getTestData
     *
     * @param string $route
     */
    public function testVisit($route, array $routes, $requestRoute, $expectedIsActive): void
    {
        $this->node->setRoute($route);
        $this->node->setAdditionalActiveRoutes($routes);
        $requestStack = $this->getRequestStackMock($requestRoute);
        $activator = new NodeActivator($requestStack);

        $activator->visit($this->node);

        $this->assertEquals($expectedIsActive, $this->node->isActive());
    }

    public function getTestData()
    {
        return [
            [null, [], 'some_route', false],
            ['some_route', ['some_other_route'], 'some_different_route', false],
            ['some_route', [], 'some_route', true],
            ['some_route', ['some_other_route'], 'some_other_route', true],
        ];
    }

    private function getRequestStackMock($requestRoute)
    {
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->getMock();
        $request->expects($this->any())->method('get')->with('_route')->will($this->returnValue($requestRoute));

        $requestStack = $this->getMockBuilder('Symfony\Component\HttpFoundation\RequestStack')->getMock();
        $requestStack->expects($this->any())->method('getCurrentRequest')->will($this->returnValue($request));

        return $requestStack;
    }
}
