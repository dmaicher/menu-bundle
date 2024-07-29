<?php

namespace Tests\Functional;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\User\InMemoryUser;

class FunctionalTest extends WebTestCase
{
    public function testRenderMenuWithUserFoo(): void
    {
        $client = $this->createClient();
        $this->loginUser($client, 'foo', 'bar', ['ROLE_FOO']);

        $crawler = $client->request('GET', '/');
        $this->assertSame(200, $client->getResponse()->getStatusCode());

        $this->assertCount(1, $crawler->filter('ul.menu-layer-1'));
        $this->assertCount(2, $crawler->filter('ul.menu-layer-2'));

        $this->assertCount(5, $crawler->filter('li.menu-item'));
        $this->assertCount(1, $crawler->filter('li.bla-class'));
        $this->assertCount(1, $crawler->filter('li.foo-child-class'));

        $this->assertCount(2, $crawler->filter('a[href="/test_2"]'));
        $this->assertCount(0, $crawler->filter('a[href="/test_1"]'));
    }

    public function testRenderMenuWithUserBar(): void
    {
        $client = $this->createClient();
        $this->loginUser($client, 'bar', 'foo', ['ROLE_BAR']);

        $crawler = $client->request('GET', '/');
        $this->assertSame(200, $client->getResponse()->getStatusCode());

        $this->assertCount(1, $crawler->filter('ul.menu-layer-1'));
        $this->assertCount(1, $crawler->filter('ul.menu-layer-2'));

        $this->assertCount(3, $crawler->filter('li.menu-item'));
        $this->assertCount(0, $crawler->filter('li.bla-class'));
        $this->assertCount(0, $crawler->filter('li.foo-child-class'));

        $this->assertCount(1, $crawler->filter('a[href="/test_2"]'));
        $this->assertCount(2, $crawler->filter('a[href="/test_1"]'));
    }

    private function loginUser(KernelBrowser $client, string $username, string $password, array $roles): void
    {
        $user = new InMemoryUser($username, $password, $roles);
        $client->loginUser($user);
    }
}
