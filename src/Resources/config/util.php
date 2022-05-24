<?php

declare(strict_types=1);

use Marlinc\UserBundle\Util\TokenGenerator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()
        ->set('marlinc.user.util.token_generator', TokenGenerator::class);
};
