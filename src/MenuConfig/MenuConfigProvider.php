<?php

namespace DAMA\MenuBundle\MenuConfig;

/**
 * @final
 */
class MenuConfigProvider
{
    /**
     * @var array<string, array>
     */
    protected $configs = [];

    /**
     * @param array<string, mixed> $config
     */
    public function addMenuConfig(string $name, array $config): void
    {
        $this->configs[$name] = $config;
    }

    /**
     * @throws \InvalidArgumentException
     *
     * @return array<mixed>
     */
    public function getMenuConfig(string $name): array
    {
        if (!isset($this->configs[$name])) {
            throw new \InvalidArgumentException("No config for '{$name}' found!");
        }

        return $this->configs[$name];
    }
}
