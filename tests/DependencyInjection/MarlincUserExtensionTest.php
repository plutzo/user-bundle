<?php

declare(strict_types=1);


namespace Marlinc\UserBundle\Tests\DependencyInjection;

use Marlinc\UserBundle\DependencyInjection\MarlincUserExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Marlinc\UserBundle\Admin\UserAdmin as EntityUserAdmin;
use Marlinc\UserBundle\Entity\UserInterface;
use Marlinc\UserBundle\Tests\Entity\User as EntityUser;
use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * @author Anton Dyshkant <vyshkant@gmail.com>
 */
final class MarlincUserExtensionTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setParameter('kernel.bundles', [
            'SonataDoctrineBundle' => true,
            'SonataAdminBundle' => true,
        ]);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testLoadDefault(): void
    {
        $this->load();
    }

    public function testImpersonatingDisabled(): void
    {
        $this->load();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument('sonata.user.twig.global', 2, false);
    }

    public function testImpersonatingEnabled(): void
    {
        $this->load([
            'impersonating' => [
                'enabled' => true,
                'route' => 'sonata_admin_dashboard',
                'parameters' => [
                    'foo' => 'bar',
                ],
            ],
        ]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument('sonata.user.twig.global', 2, true);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('sonata.user.twig.global', 3, 'sonata_admin_dashboard');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('sonata.user.twig.global', 4, ['foo' => 'bar']);
    }

    public function testTwigConfigParameterIsSetting(): void
    {
        $fakeContainer = $this->createMock(ContainerBuilder::class);

        $fakeContainer->expects(static::once())
            ->method('hasExtension')
            ->with(static::equalTo('twig'))
            ->willReturn(true);

        $fakeContainer->expects(static::once())
            ->method('prependExtensionConfig')
            ->with('twig', ['form_themes' => ['@MarlincUser/Form/form_admin_fields.html.twig']]);

        foreach ($this->getContainerExtensions() as $extension) {
            if ($extension instanceof PrependExtensionInterface) {
                $extension->prepend($fakeContainer);
            }
        }
    }

    public function testTwigConfigParameterIsSet(): void
    {
        $fakeTwigExtension = $this->createStub(TwigExtension::class);

        $fakeTwigExtension
            ->method('getAlias')
            ->willReturn('twig');

        $this->container->registerExtension($fakeTwigExtension);

        $this->load();

        $twigConfigurations = $this->container->getExtensionConfig('twig');

        static::assertArrayHasKey(0, $twigConfigurations);
        static::assertArrayHasKey('form_themes', $twigConfigurations[0]);
        static::assertSame(
            ['@MarlincUser/Form/form_admin_fields.html.twig'],
            $twigConfigurations[0]['form_themes']
        );
    }

    public function testTwigConfigParameterIsNotSet(): void
    {
        $this->load();

        $twigConfigurations = $this->container->getExtensionConfig('twig');

        static::assertArrayNotHasKey(0, $twigConfigurations);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testCorrectModelClass(): void
    {
        $this->load(['class' => ['user' => EntityUser::class]]);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testCorrectAdminClass(): void
    {
        $this->load(['admin' => ['user' => ['class' => EntityUserAdmin::class]]]);
    }
    
    /**
     * @doesNotPerformAssertions
     */
    public function testMarlincUserBundleModelClasses(): void
    {
        $this->load(['manager_type' => 'orm', 'class' => [
            'user' => UserInterface::class,
        ]]);
    }
    
    public function testMailerConfigParameterIfNotSet(): void
    {
        $this->load();

        $this->assertContainerBuilderHasAlias('sonata.user.mailer', 'sonata.user.mailer.default');
    }

    public function testMailerConfigParameter(): void
    {
        $this->load(['mailer' => 'custom.mailer.service.id']);

        $this->assertContainerBuilderHasAlias('sonata.user.mailer', 'custom.mailer.service.id');
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
