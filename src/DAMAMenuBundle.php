<?php

namespace DAMA\MenuBundle;

use DAMA\MenuBundle\DependencyInjection\CompilerPass\NodeVisitorCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class DAMAMenuBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new NodeVisitorCompilerPass());
    }
}
