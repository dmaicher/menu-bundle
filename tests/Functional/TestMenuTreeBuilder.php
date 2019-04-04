<?php

namespace Tests\Functional;

use DAMA\MenuBundle\MenuTree\MenuTreeBuilderInterface;
use DAMA\MenuBundle\Node\Node;

class TestMenuTreeBuilder implements MenuTreeBuilderInterface
{
    public function build(Node $root): void
    {
        $root
            ->child('bla')
                ->setAttr('class', 'bla-class')
                ->setRequiredPermissions(['IS_AUTHENTICATED_FULLY'])
            ->end()
            ->child('foo')
                ->setRequiredPermissions(['IS_AUTHENTICATED_ANONYMOUSLY'])
                ->setRoute('test')
                ->child('foo-child')
                    ->setAttr('class', 'foo-child-class')
                ->end()
            ->end()
            ->child('bar')
                ->setAdditionalActiveRoutes(['test_1', 'test_2'])
                ->child('bar-sub-1')
                    ->setRoute('test_1')
                    ->setRequiredPermissions(['IS_AUTHENTICATED_FULLY'])
                ->end()
                ->child('bar-sub-2')
                    ->setRoute('test_2')
                ->end()
            ->end()
        ;
    }
}
