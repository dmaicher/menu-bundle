<?php

namespace DAMA\MenuBundle\Menu;

use DAMA\MenuBundle\Node\Node;

interface MenuFactoryInterface
{
    /**
     * returns the root node of the menu
     */
    public function create(string $name): Node;
}
