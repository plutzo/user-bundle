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
