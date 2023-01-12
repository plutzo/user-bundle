<?php

use Marlinc\UserBundle\Repository\ResetPasswordRequestRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()
        ->set('marlinc.user.reset.password.request.repository', ResetPasswordRequestRepository::class)
            ->args([
                service('doctrine'),
            ])
        ->public()
    ;
};