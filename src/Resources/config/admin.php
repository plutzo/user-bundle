<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Marlinc\UserBundle\Form\Type\RolesMatrixType;
use Marlinc\UserBundle\Security\RolesBuilder\AdminRolesBuilder;
use Marlinc\UserBundle\Security\RolesBuilder\MatrixRolesBuilder;
use Marlinc\UserBundle\Security\RolesBuilder\SecurityRolesBuilder;
use Marlinc\UserBundle\Twig\RolesMatrixExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()

        ->set('marlinc.user.matrix_roles_builder', MatrixRolesBuilder::class)
            ->args([
                service('security.token_storage'),
                service('marlinc.user.admin_roles_builder'),
                service('marlinc.user.security_roles_builder'),
            ])

        ->set('marlinc.user.admin_roles_builder', AdminRolesBuilder::class)
            ->args([
                service('security.authorization_checker'),
                service('sonata.admin.pool'),
                service('sonata.admin.configuration'),
                service('translator'),
            ])

        ->set('marlinc.user.security_roles_builder', SecurityRolesBuilder::class)
            ->args([
                service('security.authorization_checker'),
                service('sonata.admin.configuration'),
                service('translator'),
                param('security.role_hierarchy.roles'),
            ])

        ->set('marlinc.user.form.roles_matrix_type', RolesMatrixType::class)
            ->public()
            ->tag('form.type')
            ->args([
                service('marlinc.user.matrix_roles_builder'),
            ])

        ->set('marlinc.user.roles_matrix_extension', RolesMatrixExtension::class)
            ->tag('twig.extension')
            ->args([
                service('marlinc.user.matrix_roles_builder'),
            ]);
};
