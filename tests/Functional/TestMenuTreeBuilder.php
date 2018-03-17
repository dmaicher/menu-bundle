<?php

namespace Tests\Functional;

use DAMA\MenuBundle\MenuTree\MenuTreeBuilderInterface;
use DAMA\MenuBundle\Node\Node;
use Symfony\Component\ExpressionLanguage\Expression;

class TestMenuTreeBuilder implements MenuTreeBuilderInterface
{
    public function build(Node $root)
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
        ;
    }
}
