<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Security\Core\User\InMemoryUser;

return function (ContainerConfigurator $container): void {
    $container->extension('framework', [
        'handle_all_throwables' => true,
        'php_errors' => [
            'log' => true,
        ],
        'session' => [
            'handler_id' => null,
            'cookie_secure' => 'auto',
            'cookie_samesite' => 'lax',
            'storage_factory_id' => 'session.storage.factory.mock_file',
        ],
    ]);

    $container->extension('security', [
        'password_hashers' => [
            InMemoryUser::class => 'plain',
        ],
    ]);
};
