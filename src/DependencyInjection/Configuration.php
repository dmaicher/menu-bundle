<?php

namespace DAMA\MenuBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @final
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('dama_menu');

        $treeBuilder->getRootNode()
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
            ->end()
        ;

        return $treeBuilder;
    }
}
