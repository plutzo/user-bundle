<?php

declare(strict_types=1);

use Marlinc\UserBundle\Action\CheckEmailAction;
use Marlinc\UserBundle\Action\CheckLoginAction;
use Marlinc\UserBundle\Action\LoginAction;
use Marlinc\UserBundle\Action\LogoutAction;
use Marlinc\UserBundle\Action\RequestAction;
use Marlinc\UserBundle\Action\ResetAction;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()
        ->set('marlinc.user.action.login', LoginAction::class)
            ->public()
            ->args([
                service('twig'),
                service('router'),
                service('security.authentication_utils'),
                service('sonata.admin.pool'),
                service('sonata.admin.global_template_registry'),
                service('security.token_storage'),
                service('translator'),
                (service('security.csrf.token_manager'))->nullOnInvalid(),
            ])

        ->set('marlinc.user.action.check_login', CheckLoginAction::class)
            ->public()

        ->set('marlinc.user.action.logout', LogoutAction::class)
            ->public();
};
