<?php

namespace Tests\NodeVisitor;

use DAMA\MenuBundle\MenuTree\MenuTreeTraverserInterface;
use DAMA\MenuBundle\Node\Node;
use DAMA\MenuBundle\NodeVisitor\NodeFilter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class NodeFilterTest extends TestCase
{
    /**
     * @var NodeFilter
     */
    private $filter;

    /**
     * @var TokenStorageInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $authChecker;

    /**
     * @var Node
     */
    private $node;

    /**
     * @var Node|MockObject
     */
    private $parent;

    public function setUp(): void
    {
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->authChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->filter = new NodeFilter($this->tokenStorage, $this->authChecker);
        $this->node = new Node();
        $this->parent = $this->getMockBuilder(Node::class)->getMock();
        $this->node->setParent($this->parent);
    }

    /**
     * @dataProvider getTestData
     */
    public function testVisit(array $permissions, $getTokenReturn, $isGrantedReturn, $expectsFiltered)
    {
        $this->node->setRequiredPermissions($permissions);

        $this->tokenStorage
            ->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue($getTokenReturn))
        ;

        $this->authChecker
            ->expects($this->any())
            ->method('isGranted')
            ->will($this->returnValue($isGrantedReturn))
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

    public function getTestData()
    {
        return array(
            array(array(), true, true, false),
            array(array('FOO'), true, true, false),
            array(array('FOO'), true, false, true),
            array(array('FOO'), false, true, true),
            array(array(new Expression('something')), false, true, true),
        );
    }
}
