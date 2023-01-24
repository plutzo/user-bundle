<?php

declare(strict_types=1);


namespace Marlinc\UserBundle\Tests\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Marlinc\UserBundle\DependencyInjection\Compiler\RolesMatrixCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Silas Joisten <silasjoisten@hotmail.de>
 */
final class RolesMatrixCompilerPassTest extends TestCase
{
    public function testProcess(): void
    {
        $definition = $this->createMock(Definition::class);

        $container = $this->createMock(ContainerBuilder::class);
        $container
            ->expects(static::once())
            ->method('getDefinition')
            ->with('sonata.user.admin_roles_builder')
            ->willReturn($definition);

        $taggedServices = [
            'sonata.admin.foo' => [0 => ['show_in_roles_matrix' => true]],
            'sonata.admin.bar' => [0 => ['show_in_roles_matrix' => false]],
            'sonata.admin.test' => [],
        ];

        $container
            ->expects(static::once())
            ->method('findTaggedServiceIds')
            ->with('sonata.admin')
            ->willReturn($taggedServices);

        $definition
            ->expects(static::once())
            ->method('addMethodCall')
            ->with('addExcludeAdmin', ['sonata.admin.bar']);

        $compilerPass = new RolesMatrixCompilerPass();
        $compilerPass->process($container);
    }
}
