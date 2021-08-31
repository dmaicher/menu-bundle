<?php

namespace DAMA\MenuBundle\NodeVisitor;

use DAMA\MenuBundle\Node\Node;

/**
 * This visitor will propagate the route of the current node to the direct parent if it does not have a route yet.
 *
 * @final
 */
class NodeRoutePropagator implements NodeVisitorInterface
{
    /**
     * {@inheritdoc}
     */
    public function visit(Node $node): void
    {
        $parent = $node->getParent();
        if ($parent && $node->hasRoute() && !$parent->hasRoute()) {
            $parent->setRoute($node->getRoute());
            $parent->setRouteParams($node->getRouteParams());
        }
    }
}
