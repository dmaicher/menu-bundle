<?php

namespace DAMA\MenuBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @final
 */
class DAMAMenuExtension extends Extension
{
    public const NODE_FACTORY_SERVICE_ID_PREFIX = 'dama_menu.node_factory.custom.';
    public const TREE_BUILDER_SERVICE_ID_PREFIX = 'dama_menu.tree_builder.custom.';

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $processedConfig = $this->processConfiguration($configuration, $configs);

        $globalConfig = [
            'node_factory' => $processedConfig['node_factory'],
            'twig_template' => $processedConfig['twig_template'],
        ];

        $loader = new Loader\PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.php');

        if (isset($processedConfig['menues'])) {
            $menuConfigProviderDef = $container->getDefinition('dama_menu.menu_config_provider');

            foreach ($processedConfig['menues'] as $name => $menuConfig) {
                $menuConfig = array_merge($globalConfig, $menuConfig);

                $menuConfig['tree_builder'] = $this->getReferenceFromConfigValue(
                    $container,
                    self::TREE_BUILDER_SERVICE_ID_PREFIX,
                    $menuConfig['tree_builder']
                );

                $menuConfig['node_factory'] = $this->getReferenceFromConfigValue(
                    $container,
                    self::NODE_FACTORY_SERVICE_ID_PREFIX,
                    $menuConfig['node_factory']
                );

                $menuConfigProviderDef->addMethodCall('addMenuConfig', [$name, $menuConfig]);
            }
        }
    }

    protected function getReferenceFromConfigValue(ContainerBuilder $container, string $prefix, string $value): Reference
    {
        if (class_exists($value) && !$container->has($value)) {
            $id = $prefix.md5($value);
            $container->register($id, $value);

            return new Reference($id);
        } else {
            return new Reference($value);
        }
    }
}
