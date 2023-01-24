<?php

declare(strict_types=1);


namespace Marlinc\UserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Christian Gripp <mail@core23.de>
 * @author Cengizhan Çalışkan <cengizhancaliskan@gmail.com>
 * @author Silas Joisten <silasjoisten@hotmail.de>
 */
final class RolesMatrixCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds('sonata.admin') as $name => $items) {
            foreach ($items as $item) {
                if (($item['show_in_roles_matrix'] ?? true) === false) {
                    $container->getDefinition('marlinc.user.admin_roles_builder')
                        ->addMethodCall('addExcludeAdmin', [$name]);
                }
            }
        }
    }
}
