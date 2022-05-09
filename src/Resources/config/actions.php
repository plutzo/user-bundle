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

use Marlinc\UserBundle\Action\CheckEmailAction;
use Marlinc\UserBundle\Action\CheckLoginAction;
use Marlinc\UserBundle\Action\LoginAction;
use Marlinc\UserBundle\Action\LogoutAction;
use Marlinc\UserBundle\Action\RequestAction;
use Marlinc\UserBundle\Action\ResetAction;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Use "service" function for creating references to services when dropping support for Symfony 4.4
    // Use "param" function for creating references to parameters when dropping support for Symfony 5.1
    $containerConfigurator->services()

        ->set('marlinc.user.action.request', RequestAction::class)
            ->public()
            ->args([
                new ReferenceConfigurator('twig'),
                new ReferenceConfigurator('router'),
                new ReferenceConfigurator('security.authorization_checker'),
                new ReferenceConfigurator('sonata.admin.pool'),
                new ReferenceConfigurator('sonata.admin.global_template_registry'),
                new ReferenceConfigurator('form.factory'),
                new ReferenceConfigurator('marlinc.user.manager.user'),
                new ReferenceConfigurator('marlinc.user.mailer'),
                new ReferenceConfigurator('marlinc.user.util.token_generator'),
                0,
            ])

        ->set('marlinc.user.action.check_email', CheckEmailAction::class)
            ->public()
            ->args([
                new ReferenceConfigurator('twig'),
                new ReferenceConfigurator('router'),
                new ReferenceConfigurator('sonata.admin.pool'),
                new ReferenceConfigurator('sonata.admin.global_template_registry'),
                0,
            ])

        ->set('marlinc.user.action.reset', ResetAction::class)
            ->public()
            ->args([
                new ReferenceConfigurator('twig'),
                new ReferenceConfigurator('router'),
                new ReferenceConfigurator('security.authorization_checker'),
                new ReferenceConfigurator('sonata.admin.pool'),
                new ReferenceConfigurator('sonata.admin.global_template_registry'),
                new ReferenceConfigurator('form.factory'),
                new ReferenceConfigurator('marlinc.user.manager.user'),
                new ReferenceConfigurator('translator'),
                0,
            ])

        ->set('marlinc.user.action.login', LoginAction::class)
            ->public()
            ->args([
                new ReferenceConfigurator('twig'),
                new ReferenceConfigurator('router'),
                new ReferenceConfigurator('security.authentication_utils'),
                new ReferenceConfigurator('sonata.admin.pool'),
                new ReferenceConfigurator('sonata.admin.global_template_registry'),
                new ReferenceConfigurator('security.token_storage'),
                new ReferenceConfigurator('translator'),
                (new ReferenceConfigurator('security.csrf.token_manager'))->nullOnInvalid(),
            ])

        ->set('marlinc.user.action.check_login', CheckLoginAction::class)
            ->public()

        ->set('marlinc.user.action.logout', LogoutAction::class)
            ->public();
};
