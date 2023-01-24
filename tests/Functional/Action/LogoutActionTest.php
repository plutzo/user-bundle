<?php

declare(strict_types=1);


namespace Marlinc\UserBundle\Tests\Functional\Action;

use Marlinc\UserBundle\Tests\App\AppKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class LogoutActionTest extends WebTestCase
{
    public function testItLogouts(): void
    {
        $client = static::createClient();
        $client->request('GET', '/logout');
        $client->followRedirect();

        static::assertRouteSame('sonata_user_admin_security_login');
    }

    /**
     * @return class-string<\Symfony\Component\HttpKernel\KernelInterface>
     */
    protected static function getKernelClass(): string
    {
        return AppKernel::class;
    }
}
