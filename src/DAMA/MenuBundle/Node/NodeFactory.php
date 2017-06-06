<?php

namespace DAMA\MenuBundle\Node;

class NodeFactory implements NodeFactoryInterface
{
    /**
     * @param $label
     *
     * @return Node
     */
    public function create($label = null)
    {
        $node = new Node($label);
        $node->setNodeFactory($this);

        return $node;
    }
}
