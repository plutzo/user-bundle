<?php

declare(strict_types=1);


namespace Marlinc\UserBundle\Tests\Util;

use Marlinc\UserBundle\Util\EmailCanonicalizer;
use PHPUnit\Framework\TestCase;
use Marlinc\UserBundle\Tests\App\Entity\User;

final class CanonicalFieldsUpdaterTest extends TestCase
{
    public function testUpdateCanonicalFields(): void
    {
        $user = new User();
        $user->setEmail(EmailCanonicalizer::canonicalize('User@Example.com'));

        static::assertSame('user@example.com', $user->getEmail());
    }
}
