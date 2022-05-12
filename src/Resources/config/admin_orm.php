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

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {

    $containerConfigurator->services()

        ->set('marlinc.user.admin.user')
            ->tag('sonata.admin', [
                'model_class' => '%marlinc.user.user.class%',
                'controller' => '%marlinc.user.admin.user.controller%',
                'manager_type' => 'orm',
                'group' => 'marlinc_user',
                'label' => 'users',
                'translation_domain' => 'MarlincUserBundle',
                'label_translator_strategy' => 'sonata.admin.label.strategy.underscore',
                'icon' => '<i class=\'fa fa-users\'></i>',
            ])
            ->args([
                service('marlinc.user.manager.user'),
            ]);
};
