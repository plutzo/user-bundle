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

use Marlinc\UserBundle\Command\ActivateUserCommand;
use Marlinc\UserBundle\Command\ChangePasswordCommand;
use Marlinc\UserBundle\Command\CreateUserCommand;
use Marlinc\UserBundle\Command\DeactivateUserCommand;
use Marlinc\UserBundle\Command\DemoteUserCommand;
use Marlinc\UserBundle\Command\PromoteUserCommand;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {

    $containerConfigurator->services()

        ->set('marlinc.user.command.activate_user', ActivateUserCommand::class)
            ->tag('console.command')
            ->args([
                service('marlinc.user.manager.user'),
            ])

        ->set('marlinc.user.command.change_password', ChangePasswordCommand::class)
            ->tag('console.command')
            ->args([
                service('marlinc.user.manager.user'),
            ])

        ->set('marlinc.user.command.create_user', CreateUserCommand::class)
            ->tag('console.command')
            ->args([
                service('marlinc.user.manager.user'),
            ])

        ->set('marlinc.user.command.deactivate_user', DeactivateUserCommand::class)
            ->tag('console.command')
            ->args([
                service('marlinc.user.manager.user'),
            ])

        ->set('marlinc.user.command.promote_user', PromoteUserCommand::class)
            ->tag('console.command')
            ->args([
                service('marlinc.user.manager.user'),
            ])

        ->set('marlinc.user.command.demote_user', DemoteUserCommand::class)
            ->tag('console.command')
            ->args([
                service('marlinc.user.manager.user'),
            ]);
};
