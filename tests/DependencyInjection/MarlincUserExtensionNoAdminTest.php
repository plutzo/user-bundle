<?php

declare(strict_types=1);


namespace Marlinc\UserBundle\Tests\DependencyInjection;

use Marlinc\UserBundle\DependencyInjection\MarlincUserExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Alexandr Zolotukhin <alex@alexandrz.com>
 */
final class MarlincUserExtensionNoAdminTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setParameter('kernel.bundles', [
            'SonataDoctrineBundle' => true,
        ]);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testLoadDefault(): void
    {
        $this->load();
    }

    public function testGetGlobalVariablesService(): void
    {
        $this->load();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'marlinc.user.twig.global',
            0,
            new Reference('sonata.admin.pool', ContainerInterface::NULL_ON_INVALID_REFERENCE)
        );
    }

    /**
     * @return mixed[]
     */
    protected function getMinimalConfiguration(): array
    {
        return [
            'resetting' => [
                'email' => [
                    'address' => 'sonata@localhost.com',
                    'sender_name' => 'Sonata',
                ],
            ],
        ];
    }

    protected function getContainerExtensions(): array
    {
        return [
            new MarlincUserExtension(),
        ];
    }
}
