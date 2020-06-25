<?php

namespace DAMA\MenuBundle\NodeVisitor;

use DAMA\MenuBundle\MenuTree\MenuTreeTraverserInterface;
use DAMA\MenuBundle\Node\Node;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * This visitor will remove the node from the tree if not all necessary permissions are granted.
 */
class NodeFilter implements NodeVisitorInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authChecker;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authChecker
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->authChecker = $authChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function visit(Node $node)
    {
        foreach ($node->getRequiredPermissions() as $permission) {
            if (!$this->tokenStorage->getToken() || !$this->authChecker->isGranted($permission)) {
                $node->getParent()->removeChild($node);

                return MenuTreeTraverserInterface::STOP_TRAVERSAL;
            }
        }
    }
}
