<?php

namespace DAMA\MenuBundle\NodeVisitor;

use DAMA\MenuBundle\Node\Node;

interface NodeVisitorInterface
{
    /**
     * @param Node $node
     *
     * @return mixed
     */
    public function visit(Node $node);
}
