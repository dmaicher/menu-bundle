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

    public function setUp()
    {
        $this->node = new Node();
    }

    /**
     * @dataProvider getTestData
     *
     * @param string $route
     * @param array $routes
     * @param $requestRoute
     * @param $expectedIsActive
     */
    public function testVisit($route, array $routes, $requestRoute, $expectedIsActive)
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
        return array(
            array(null, array(), 'some_route', false),
            array('some_route', array('some_other_route'), 'some_different_route', false),
            array('some_route', array(), 'some_route', true),
            array('some_route', array('some_other_route'), 'some_other_route', true),
        );
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
