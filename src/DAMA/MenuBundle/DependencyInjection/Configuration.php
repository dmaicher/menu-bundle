<?php

namespace DAMA\MenuBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('dama_menu');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('node_factory')->defaultValue('dama_menu.node_factory')->end()
                ->scalarNode('twig_template')->defaultValue('DMMenuBundle::menu.html.twig')->end()
                ->arrayNode('menues')
                    ->requiresAtLeastOneElement()
                    ->prototype('array')
                        ->children()
                            ->scalarNode('tree_builder')->isRequired()->end()
                            ->scalarNode('node_factory')->end()
                            ->scalarNode('twig_template')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
