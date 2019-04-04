<?php

namespace Tests\Functional\MenuTree;

use DAMA\MenuBundle\Menu\MenuFactoryInterface;
use DAMA\MenuBundle\Node\Node;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MenuTreeTraverserTest extends WebTestCase
{
    /**
     * @var Client
     */
    private $webClient;

    public function setUp()
    {
        parent::setUp();

        $this->webClient = $this->createClient();
    }

    public function testMenu(): void
    {
        $children = $this
            ->getMenuFactory()
            ->create('test_menu')
            ->getChildren()
        ;

        $this->assertCount(1, $children);

        /** @var Node $child */
        $child = reset($children);

        $this->assertSame('test_2', $child->getRoute());
    }

    private function getMenuFactory(): MenuFactoryInterface
    {
        return self::$container->get('dama_menu.menu_factory');
    }
}
