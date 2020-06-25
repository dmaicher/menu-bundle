<?php

namespace Tests\MenuConfig;

use DAMA\MenuBundle\MenuConfig\MenuConfigProvider;
use PHPUnit\Framework\TestCase;

class MenuConfigProviderTest extends TestCase
{
    public function testAddMenuDefinitionAndGetMenuDefinition(): void
    {
        $provider = new MenuConfigProvider();
        $config = ['some_parameter' => true];
        $provider->addMenuConfig('name', $config);

        $this->assertSame($config, $provider->getMenuConfig('name'));
    }

    public function testGetMenuDefinitionThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $provider = new MenuConfigProvider();
        $provider->getMenuConfig('some_not_existing_name');
    }
}
