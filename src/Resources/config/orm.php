<?php

declare(strict_types=1);

use Marlinc\UserBundle\Entity\UserManager;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()
        ->set('marlinc.user.manager.user', UserManager::class)
            ->args([
                param('marlinc.user.user.class'),
                service('doctrine'),
                service('security.password_hasher'),
            ]);
};
