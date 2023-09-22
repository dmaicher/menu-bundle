<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\User\InMemoryUser;

return function (ContainerConfigurator $container): void {
    $securityConfig = [
        'password_hashers' => [
            InMemoryUser::class => 'plain',
        ],
    ];

    if (Kernel::VERSION_ID < 70000) {
        $securityConfig['enable_authenticator_manager'] = true;
    }

    $container->extension('framework', [
        'session' => ['storage_factory_id' => 'session.storage.factory.mock_file'],
    ]);

    $container->extension('security', $securityConfig);
};
