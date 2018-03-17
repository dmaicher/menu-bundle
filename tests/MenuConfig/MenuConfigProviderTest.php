<?php

namespace Tests\MenuConfig;

use DAMA\MenuBundle\MenuConfig\MenuConfigProvider;
use PHPUnit\Framework\TestCase;

class MenuConfigProviderTest extends TestCase
{
    public function testAddMenuDefinitionAndGetMenuDefinition()
    {
        $provider = new MenuConfigProvider();
        $config = array('some_parameter' => true);
        $provider->addMenuConfig('name', $config);

        $this->assertSame($config, $provider->getMenuConfig('name'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetMenuDefinitionThrowsException()
    {
        $provider = new MenuConfigProvider();
        $provider->getMenuConfig('some_not_existing_name');
    }
}