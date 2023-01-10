<?php

use Marlinc\UserBundle\Controller\ResetPasswordController;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()
        ->set('marlinc.user.reset.password', ResetPasswordController::class)
            ->args([
                service('symfonycasts.reset_password.helper'),
                service('doctrine.orm.default_entity_manager'),
            ])
        ->tag('controller.service_arguments')
        ->call('setContainer', [new ReferenceConfigurator(ContainerInterface::class)])
        ->public()
    ;
};