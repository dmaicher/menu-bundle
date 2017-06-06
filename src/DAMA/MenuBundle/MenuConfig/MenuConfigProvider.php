<?php

namespace DAMA\MenuBundle\MenuConfig;

class MenuConfigProvider
{
    /**
     * @var array
     */
    protected $configs = array();

    /**
     * @param $name
     * @param MenuConfig $config
     */
    public function addMenuConfig($name, array $config)
    {
        $this->configs[$name] = $config;
    }

    /**
     * @param $name
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function getMenuConfig($name)
    {
        if (!isset($this->configs[$name])) {
            throw new \InvalidArgumentException("No config for '{$name}' found!");
        }

        return $this->configs[$name];
    }
}
