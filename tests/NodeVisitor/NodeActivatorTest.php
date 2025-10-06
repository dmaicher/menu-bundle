<?php

declare(strict_types=1);

namespace Tests\NodeVisitor;

use DAMA\MenuBundle\Node\Node;
use DAMA\MenuBundle\NodeVisitor\NodeActivator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class NodeActivatorTest extends TestCase
{
    protected Node $node;

    public function setUp(): void
    {
        $this->node = new Node();
    }

    #[DataProvider('getTestData')]
    public function testVisit(?string $route, array $routes, $requestRoute, $expectedIsActive): void
    {
        $this->node->setRoute($route);
        $this->node->setAdditionalActiveRoutes($routes);
        $requestStack = $this->getRequestStack($requestRoute);
        $activator = new NodeActivator($requestStack);

        $activator->visit($this->node);

        $this->assertEquals($expectedIsActive, $this->node->isActive());
    }

    public static function getTestData(): array
    {
        return [
            [null, [], 'some_route', false],
            ['some_route', ['some_other_route'], 'some_different_route', false],
            ['some_route', [], 'some_route', true],
            ['some_route', ['some_other_route'], 'some_other_route', true],
        ];
    }

    private function getRequestStack(string $requestRoute): RequestStack
    {
        $request = new Request();
        $request->attributes->set('_route', $requestRoute);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        return $requestStack;
    }
}
