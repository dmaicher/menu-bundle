<?php

namespace DAMA\MenuBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class DAMAMenuExtension extends Extension
{
    const NODE_FACTORY_SERVICE_ID_PREFIX = 'dama_menu.node_factory.custom.';
    const TREE_BUILDER_SERVICE_ID_PREFIX = 'dama_menu.tree_builder.custom.';

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $processedConfig = $this->processConfiguration($configuration, $configs);

        $globalConfig = array(
            'node_factory' => $processedConfig['node_factory'],
            'twig_template' => $processedConfig['twig_template'],
        );

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

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

                $menuConfigProviderDef->addMethodCall('addMenuConfig', array($name, $menuConfig));
            }
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param $prefix
     * @param $value
     *
     * @return Reference
     */
    protected function getReferenceFromConfigValue(ContainerBuilder $container, $prefix, $value)
    {
        if (class_exists($value)) {
            $id = $prefix.md5($value);
            $container->register($id, $value);

            return new Reference($id);
        } else {
            return new Reference($value);
        }
    }
}
