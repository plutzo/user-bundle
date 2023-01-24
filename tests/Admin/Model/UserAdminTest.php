<?php

declare(strict_types=1);


namespace Marlinc\UserBundle\Tests\Admin\Model;

use PHPUnit\Framework\TestCase;
use Marlinc\UserBundle\Admin\UserAdmin;
use Marlinc\UserBundle\Entity\UserManagerInterface;

/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 */
final class UserAdminTest extends TestCase
{
    public function testInstance(): void
    {
        $admin = new UserAdmin($this->createStub(UserManagerInterface::class));

        static::assertNotEmpty($admin);
    }
}
