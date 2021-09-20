<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Core\User\User;

return function (ContainerConfigurator $container): void {
    if (class_exists(InMemoryUser::class)) {
        $sessionConfig = ['storage_factory_id' => 'session.storage.factory.mock_file'];
        $securityConfig = [
            'password_hashers' => [
                InMemoryUser::class => 'plain',
            ],
            'enable_authenticator_manager' => true,
        ];
    } else {
        $sessionConfig = ['storage_id' => 'session.storage.mock_file'];
        $securityConfig = [
            'encoders' => [
                User::class => 'plain',
            ],
        ];
    }

    $container->extension('framework', [
        'session' => $sessionConfig,
    ]);

    $container->extension('security', $securityConfig);
};
