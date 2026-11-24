<?php

namespace DAMA\MenuBundle\NodeVisitor;

use DAMA\MenuBundle\Node\Node;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * This visitor will set a node to active if one of its routes matches the current route of the request.
 */
final class NodeActivator implements NodeVisitorInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
    }

    public function visit(Node $node): void
    {
        if (!$request = $this->requestStack->getCurrentRequest()) {
            return;
        }

        if (in_array($request->attributes->get('_route'), $node->getAllActiveRoutes(), true)) {
            $node->setActive(true);

            return;
        }

        if (($callable = $node->getAdditionalActiveRequestMatcher())
            && $callable($request)
        ) {
            $node->setActive(true);
        }
    }
}
