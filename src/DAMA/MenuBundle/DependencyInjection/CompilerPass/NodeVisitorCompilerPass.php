<?php

namespace DAMA\MenuBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class NodeVisitorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('dama_menu.menu_tree_traverser')) {
            return;
        }

        $definition = $container->getDefinition(
            'dama_menu.menu_tree_traverser'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'dama_menu.node_visitor'
        );

        uksort($taggedServices, function ($i, $j) use ($taggedServices) {
            return $taggedServices[$i][0]['priority'] -  $taggedServices[$j][0]['priority'];
        });

        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall(
                'addVisitor',
                array(new Reference($id))
            );
        }
    }
}
