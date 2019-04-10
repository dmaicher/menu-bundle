<?php

namespace Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FunctionalTest extends WebTestCase
{
    public function testRenderMenuOne(): void
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');
        $this->assertSame(200, $client->getResponse()->getStatusCode());

        $this->assertCount(1, $crawler->filter('ul.menu-layer-1'));
        $this->assertCount(2, $crawler->filter('ul.menu-layer-2'));

        $this->assertCount(4, $crawler->filter('li.menu-item'));
        $this->assertCount(0, $crawler->filter('li.bla-class'));
        $this->assertCount(1, $crawler->filter('li.foo-child-class'));

        $this->assertCount(2, $crawler->filter('a[href="/test_2"]'));
        $this->assertCount(0, $crawler->filter('a[href="/test_1"]'));
    }
}
