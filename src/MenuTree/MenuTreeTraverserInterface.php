<?php

namespace DAMA\MenuBundle\MenuTree;

use DAMA\MenuBundle\Node\Node;

interface MenuTreeTraverserInterface
{
    /**
     * Indicates that traversal on current path should be stopped. So no further traversal below current node.
     */
    public const STOP_TRAVERSAL = 0;

    public function traverse(Node $node): void;
}
