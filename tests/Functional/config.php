<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Security\Core\User\InMemoryUser;

return function (ContainerConfigurator $container): void {
    $container->extension('framework', [
        'session' => ['storage_factory_id' => 'session.storage.factory.mock_file'],
    ]);

    $container->extension('security', [
        'password_hashers' => [
            InMemoryUser::class => 'plain',
        ],
    ]);
};
