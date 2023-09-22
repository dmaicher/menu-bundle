<?php

namespace Tests\Functional;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Core\User\User;

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
        if (class_exists(InMemoryUser::class)) {
            $user = new InMemoryUser($username, $password, $roles);
        } else {
            $user = new User($username, $password, $roles);
        }

        if (method_exists($client, 'loginUser')) {
            $client->loginUser($user);

            return;
        }

        // TODO: cleanup once Symfony 4.4 support is dropped
        $token = new UsernamePasswordToken($user, $user->getPassword(), 'main', $user->getRoles());

        $container = self::$kernel->getContainer()->get('test.service_container');
        $container->get('security.untracked_token_storage')->setToken($token);

        if (!$container->has('session') && !$container->has('session_factory')) {
            return;
        }

        $session = $container->get($container->has('session') ? 'session' : 'session_factory');
        $session->set('_security_main', serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
    }
}
