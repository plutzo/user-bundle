<?php

declare(strict_types=1);

use Marlinc\UserBundle\Security\UserProvider;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()
        ->set('marlinc.user.security.user_provider', UserProvider::class)
            ->args([
                service('marlinc.user.manager.user'),
            ]);
};
