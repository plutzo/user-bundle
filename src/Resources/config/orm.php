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

use Marlinc\UserBundle\Entity\UserManager;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Security\Http\Authentication\AuthenticatorManager;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {

    $passwordHasherId = 'security.password_hasher';

    $containerConfigurator->services()

        ->set('marlinc.user.manager.user', UserManager::class)
            ->args([
                '%marlinc.user.user.class%',
                service('doctrine'),
                service('marlinc.user.util.canonical_fields_updater'),
                service($passwordHasherId),
            ]);
};
