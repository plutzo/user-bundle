<?php

declare(strict_types=1);

use Marlinc\UserBundle\Twig\GlobalVariables;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()
        ->set('marlinc.user.twig.global', GlobalVariables::class)
            ->args([
                (service('sonata.admin.pool'))->nullOnInvalid(),
                '',
                false,
                '',
                [],
            ]);
};
