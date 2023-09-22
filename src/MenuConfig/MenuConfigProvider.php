<?php

namespace DAMA\MenuBundle\MenuConfig;

final class MenuConfigProvider
{
    /**
     * @var array<string, array<string, mixed>>
     */
    private $configs = [];

    /**
     * @param array<string, mixed> $config
     */
    public function addMenuConfig(string $name, array $config): void
    {
        $this->configs[$name] = $config;
    }

    /**
     * @return array<string, mixed>
     *
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
