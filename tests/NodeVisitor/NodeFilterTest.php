<?php

declare(strict_types=1);

namespace Tests\NodeVisitor;

use DAMA\MenuBundle\MenuTree\MenuTreeTraverserInterface;
use DAMA\MenuBundle\Node\Node;
use DAMA\MenuBundle\Node\NodeFactory;
use DAMA\MenuBundle\NodeVisitor\NodeFilter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class NodeFilterTest extends TestCase
{
    private NodeFilter $filter;
    private TokenStorageInterface&MockObject $tokenStorage;
    private AuthorizationCheckerInterface&MockObject $authChecker;
    private Node $node;
    private Node&MockObject $parent;
    private TokenInterface&MockObject $token;

    public function setUp(): void
    {
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->authChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->filter = new NodeFilter($this->tokenStorage, $this->authChecker);
        $this->node = new Node();
        $this->parent = $this->createMock(Node::class);
        $this->node->setParent($this->parent);
        $this->token = $this->createMock(TokenInterface::class);
    }

    #[DataProvider('getTestData')]
    public function testVisit(array $permissions, bool $hasToken, $isGrantedReturn, $expectsFiltered): void
    {
        $this->node->setRequiredPermissions($permissions);

        $this->tokenStorage
            ->expects($this->any())
            ->method('getToken')
            ->willReturn($hasToken ? $this->token : null)
        ;

        $this->authChecker
            ->expects($this->any())
            ->method('isGranted')
            ->willReturn($isGrantedReturn)
        ;

        if ($expectsFiltered) {
            $this->parent->expects($this->once())->method('removeChild');
        } else {
            $this->parent->expects($this->never())->method('removeChild');
        }

        $return = $this->filter->visit($this->node);

        if ($expectsFiltered) {
            $this->assertSame(MenuTreeTraverserInterface::STOP_TRAVERSAL, $return);
        } else {
            $this->assertNotSame(MenuTreeTraverserInterface::STOP_TRAVERSAL, $return);
        }
    }

    #[TestWith([true])]
    #[TestWith([false])]
    public function testRemoveParentIfNoActiveChildren(bool $remove): void
    {
        $tree = (new NodeFactory())->create();
        $tree
            ->child('foo')
                ->setRemoveIfNoChildren($remove)
                ->child('bar')
                    ->setRequiredPermissions(['foo'])
                ->end()
            ->end()
        ;

        $this->tokenStorage
            ->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue(null))
        ;

        $children = $tree->getChildren();
        $foo = reset($children);
        $this->filter->visit($foo);
        $this->assertCount(1, $tree->getChildren());

        $children = $foo->getChildren();
        $bar = reset($children);
        $this->filter->visit($bar);
        $this->assertCount($remove ? 0 : 1, $tree->getChildren());
    }

    public static function getTestData(): array
    {
        return [
            [[], true, true, false],
            [['FOO'], true, true, false],
            [['FOO'], true, false, true],
            [['FOO'], false, true, true],
            [[new Expression('something')], false, true, true],
        ];
    }
}
