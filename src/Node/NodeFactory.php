<?php

namespace DAMA\MenuBundle\Node;

final class NodeFactory implements NodeFactoryInterface
{
    public function create(string $label = null): Node
    {
        $node = new Node($label);
        $node->setNodeFactory($this);

        return $node;
    }
}
