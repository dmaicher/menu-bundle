<?php

namespace Tests\Functional;

use DAMA\MenuBundle\MenuTree\MenuTreeBuilderInterface;
use DAMA\MenuBundle\Node\Node;
use Symfony\Component\HttpFoundation\Request;

class TestMenuTreeBuilder implements MenuTreeBuilderInterface
{
    public function build(Node $root): void
    {
        $root
            ->child('bla')
                ->setAttr('class', 'bla-class')
                ->setRequiredPermissions(['ROLE_FOO'])
            ->end()
            ->child('foo')
                ->setRequiredPermissions(['ROLE_FOO'])
                ->setRoute('test')
                ->child('foo-child')
                    ->setAttr('class', 'foo-child-class')
                ->end()
            ->end()
            ->child('bar')
                ->setAdditionalActiveRoutes(['test_1', 'test_2'])
                ->child('bar-sub-1')
                    ->setRoute('test_1')
                    ->setRequiredPermissions(['ROLE_BAR'])
                ->end()
                ->child('bar-sub-2')
                    ->setRoute('test_2')
                    ->setAdditionalActiveRequestMatcher(static function (Request $request): bool {
                        return str_starts_with($request->getPathInfo(), '/test_');
                    })
                ->end()
            ->end()
        ;
    }
}
