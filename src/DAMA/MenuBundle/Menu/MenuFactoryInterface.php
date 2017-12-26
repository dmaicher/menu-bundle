<?php

namespace DAMA\MenuBundle\Menu;

interface MenuFactoryInterface
{
    /**
     * @param $name
     *
     * @return Menu
     */
    public function create($name);
}
