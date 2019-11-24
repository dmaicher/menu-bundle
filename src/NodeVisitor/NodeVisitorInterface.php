<?php

namespace DAMA\MenuBundle\NodeVisitor;

use DAMA\MenuBundle\Node\Node;

interface NodeVisitorInterface
{
    /**
     * @return void|int
     */
    public function visit(Node $node);
}
