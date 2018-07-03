<?php

declare(strict_types=1);

namespace Marlinc\UserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * GlobalVariablesCompilerPass.
 *
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class GlobalVariablesCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $container->getDefinition('twig')
            ->addMethodCall('addGlobal', ['marlinc_user', new Reference('marlinc.user.twig.global')]);
    }
}
