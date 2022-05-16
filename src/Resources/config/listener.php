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

use Marlinc\UserBundle\Listener\LastLoginListener;
use Marlinc\UserBundle\Listener\UserListener;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {

    $containerConfigurator->services()

        ->set('marlinc.user.listener.user', UserListener::class)
            ->tag('doctrine.event_subscriber')
            ->args([
                service('marlinc.user.util.canonical_fields_updater'),
                service('marlinc.user.manager.user'),
            ])

        ->set('marlinc.user.listener.last_login', LastLoginListener::class)
            ->tag('kernel.event_subscriber')
            ->args([
                service('marlinc.user.manager.user'),
            ]);
};
