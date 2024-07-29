<?php

namespace DAMA\MenuBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class MenuCompilerPass implements CompilerPassInterface
{
    public const NODE_FACTORY_SERVICE_ID_PREFIX = 'dama_menu.node_factory.custom.';
    public const TREE_BUILDER_SERVICE_ID_PREFIX = 'dama_menu.tree_builder.custom.';

    public function process(ContainerBuilder $container): void
    {
        /** @var array{
         *   node_factory: string,
         *   twig_template: string,
         *   menues?: array<string, array{
         *     tree_builder: string,
         *     twig_template: string
         *  }>
         * } $processedConfig
         */
        $processedConfig = $container->getParameter('dama.menu.config');

        $globalConfig = [
            'node_factory' => $processedConfig['node_factory'],
            'twig_template' => $processedConfig['twig_template'],
        ];

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
        }

        return new Reference($value);
    }
}
