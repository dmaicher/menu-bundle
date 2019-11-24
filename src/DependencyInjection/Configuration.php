<?php

namespace DAMA\MenuBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('dama_menu');
        // Keep compatibility with symfony/config < 4.2
        if (!method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->root('dama_menu');
        } else {
            $rootNode = $treeBuilder->getRootNode();
        }

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('node_factory')->defaultValue('dama_menu.node_factory')->end()
                ->scalarNode('twig_template')->defaultValue('@DAMAMenu/menu.html.twig')->end()
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
