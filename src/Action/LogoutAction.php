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

namespace Marlinc\UserBundle\Action;

final class LogoutAction
{
    public function __invoke(): void
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }
}
