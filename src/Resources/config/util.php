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

use Marlinc\UserBundle\Util\CanonicalFieldsUpdater;
use Marlinc\UserBundle\Util\TokenGenerator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->services()

        ->set('marlinc.user.util.canonical_fields_updater', CanonicalFieldsUpdater::class)

        ->set('marlinc.user.util.token_generator', TokenGenerator::class);
};
