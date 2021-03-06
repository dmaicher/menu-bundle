<?php

namespace DAMA\MenuBundle\NodeVisitor;

use DAMA\MenuBundle\Node\Node;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * This visitor will set a node to active if one of its routes matches the current route of the request.
 */
class NodeActivator implements NodeVisitorInterface
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function visit(Node $node): void
    {
        if (!$request = $this->requestStack->getCurrentRequest()) {
            return;
        }

        if (in_array($request->get('_route'), $node->getAllActiveRoutes())) {
            $node->setActive(true);
        }
    }
}
