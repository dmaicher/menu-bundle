<?php

namespace DAMA\MenuBundle\Menu;

use DAMA\MenuBundle\Node\Node;

interface MenuFactoryInterface
{
    /**
     * @param $name
     *
     * @return Node
     */
    public function create($name);
}
