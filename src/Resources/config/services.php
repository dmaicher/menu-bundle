<?php

use DAMA\MenuBundle\Menu\MenuFactory;
use DAMA\MenuBundle\MenuConfig\MenuConfigProvider;
use DAMA\MenuBundle\MenuTree\MenuTreeTraverser;
use DAMA\MenuBundle\Node\NodeFactory;
use DAMA\MenuBundle\NodeVisitor\NodeActivator;
use DAMA\MenuBundle\NodeVisitor\NodeFilter;
use DAMA\MenuBundle\NodeVisitor\NodeRoutePropagator;
use DAMA\MenuBundle\Twig\MenuExtension;
use DAMA\MenuBundle\Twig\MenuRuntime;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;

return function (ContainerConfigurator $container): void {
    $container->services()
        ->set('dama_menu.twig.menu_extension', MenuExtension::class)
        ->tag('twig.extension')
    ;

    $container->services()
        ->set('dama_menu.twig.menu_runtime', MenuRuntime::class)
        ->tag('twig.runtime')
        ->arg(0, new Reference('twig'))
        ->arg(1, new Reference('dama_menu.menu_factory'))
        ->arg(2, new Reference('dama_menu.menu_config_provider'))
    ;

    $container->services()
        ->set('dama_menu.node_factory', NodeFactory::class)
    ;

    $container->services()
        ->set('dama_menu.menu_factory', MenuFactory::class)
        ->arg(0, new Reference('dama_menu.menu_config_provider'))
        ->arg(1, new Reference('dama_menu.menu_tree_traverser'))
    ;

    $container->services()
        ->set('dama_menu.menu_config_provider', MenuConfigProvider::class)
    ;

    $container->services()
        ->set('dama_menu.menu_tree_traverser', MenuTreeTraverser::class)
    ;

    $container->services()
        ->set('dama_menu.node_visitor.filter', NodeFilter::class)
        ->arg(0, new Reference('security.token_storage'))
        ->arg(1, new Reference('security.authorization_checker'))
        ->tag('dama_menu.node_visitor', [
            'priority' => 3,
        ])
    ;

    $container->services()
        ->set('dama_menu.node_visitor.activator', NodeActivator::class)
        ->arg(0, new Reference('request_stack'))
        ->tag('dama_menu.node_visitor', [
            'priority' => 2,
        ])
    ;

    $container->services()
        ->set('dama_menu.node_visitor.node_route_propagator', NodeRoutePropagator::class)
        ->arg(0, new Reference('request_stack'))
        ->tag('dama_menu.node_visitor', [
            'priority' => 1,
        ])
    ;
};
