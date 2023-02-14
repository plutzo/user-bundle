<?php

declare(strict_types=1);

use Marlinc\UserBundle\Action\CheckEmailAction;
use Marlinc\UserBundle\Action\LoginAction;
use Marlinc\UserBundle\Action\RequestPasswordResetAction;
use Marlinc\UserBundle\Action\ResetPasswordAction;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()
        ->set(LoginAction::class)
            ->public()
            ->args([
                service('twig'),
                service('router'),
                service('security.authentication_utils'),
                service('sonata.admin.pool'),
                service('sonata.admin.global_template_registry'),
                service('security.token_storage'),
                service('translator')
            ])
        ->set(RequestPasswordResetAction::class)
            ->public()
            ->args([
                service('twig'),
                service('mailer.mailer'),
                service('security.http_utils'),
                service('form.factory'),
                service('sonata.admin.global_template_registry'),
                service('symfonycasts.reset_password.helper'),
                service('marlinc.user.manager.user'),
                service('translator'),
                [],
                ''
            ])
        ->set(CheckEmailAction::class)
            ->public()
            ->args([
                service('twig'),
                service('sonata.admin.global_template_registry'),
                service('symfonycasts.reset_password.helper')
            ])
        ->set(ResetPasswordAction::class)
            ->public()
            ->args([
                service('twig'),
                service('security.http_utils'),
                service('form.factory'),
                service('sonata.admin.global_template_registry'),
                service('symfonycasts.reset_password.helper'),
                service('marlinc.user.manager.user')
            ])
    ;
};
