<?php

namespace DAMA\MenuBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class NodeVisitorCompilerPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('dama_menu.menu_tree_traverser')) {
            return;
        }

        // if no firewall/security is configured then simply remove the node filter
        if (!$container->hasDefinition('security.token_storage')) {
            $container->removeDefinition('dama_menu.node_visitor.filter');
        }

        $definition = $container->getDefinition(
            'dama_menu.menu_tree_traverser'
        );

        $taggedServices = $this->findAndSortTaggedServices('dama_menu.node_visitor', $container);

        foreach ($taggedServices as $id) {
            $definition->addMethodCall('addVisitor', [new Reference($id)]);
        }
    }
}
