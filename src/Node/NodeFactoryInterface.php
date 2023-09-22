<?php

namespace DAMA\MenuBundle\Node;

interface NodeFactoryInterface
{
    public function create(string $label = null): Node;
}
