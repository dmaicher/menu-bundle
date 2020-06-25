<?php

namespace DAMA\MenuBundle\MenuConfig;

class MenuConfigProvider
{
    /**
     * @var array
     */
    protected $configs = [];

    public function addMenuConfig(string $name, array $config): void
    {
        $this->configs[$name] = $config;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function getMenuConfig(string $name): array
    {
        if (!isset($this->configs[$name])) {
            throw new \InvalidArgumentException("No config for '{$name}' found!");
        }

        return $this->configs[$name];
    }
}
