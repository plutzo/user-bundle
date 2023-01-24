<?php

declare(strict_types=1);


namespace Marlinc\UserBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Marlinc\UserBundle\Admin\UserAdmin;
use Marlinc\UserBundle\DependencyInjection\Configuration;
use Marlinc\UserBundle\Entity\BaseUser;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    public function testMinimalConfigurationRequired(): void
    {
        $this->assertConfigurationIsInvalid([]);
        $this->assertConfigurationIsValid([
            'sonata_user' => [
                'resetting' => [
                    'email' => [
                        'address' => 'sonata@localhost.com',
                        'sender_name' => 'Sonata Admin',
                    ],
                ],
            ],
        ]);
    }

    public function testDefault(): void
    {
        $this->assertProcessedConfigurationEquals([
            [
                'resetting' => [
                    'email' => [
                        'address' => 'sonata@localhost.com',
                        'sender_name' => 'Sonata Admin',
                    ],
                ],
            ],
        ], [
            'security_acl' => false,
            'impersonating' => [
                'enabled' => false,
                'parameters' => [],
            ],
            'manager_type' => 'orm',
            'class' => [
                'user' => BaseUser::class,
            ],
            'admin' => [
                'user' => [
                    'class' => UserAdmin::class,
                    'controller' => '%sonata.admin.configuration.default_controller%',
                    'translation' => 'MarlincUserBundle',
                ],
            ],
            'profile' => [
                'default_avatar' => 'bundles/sonatauser/default_avatar.png',
            ],
            'mailer' => 'sonata.user.mailer.default',
            'resetting' => [
                'retry_ttl' => 7200,
                'token_ttl' => 86400,
                'email' => [
                    'address' => 'sonata@localhost.com',
                    'sender_name' => 'Sonata Admin',
                    'template' => '@MarlincUser/Admin/Security/Resetting/email.html.twig',
                ],
            ],
        ]);
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }
}
