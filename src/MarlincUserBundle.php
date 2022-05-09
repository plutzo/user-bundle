<?php

namespace Marlinc\UserBundle;

use Marlinc\UserBundle\DependencyInjection\Compiler\GlobalVariablesCompilerPass;
use Marlinc\UserBundle\DependencyInjection\Compiler\RolesMatrixCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MarlincUserBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new GlobalVariablesCompilerPass());
        $container->addCompilerPass(new RolesMatrixCompilerPass());
    }
}