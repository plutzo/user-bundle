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

namespace Marlinc\UserBundle\Mailer;

use Marlinc\UserBundle\Entity\UserInterface;

interface MailerInterface
{
    public function sendConfirmationEmailMessage(UserInterface $user): void;

    public function sendResettingEmailMessage(UserInterface $user): void;
}
