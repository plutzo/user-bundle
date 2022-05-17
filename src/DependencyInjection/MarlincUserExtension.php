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

namespace Marlinc\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
final class MarlincUserExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container): void
    {
        // get all bundles
        $bundles = $container->getParameter('kernel.bundles');

        if (isset($bundles['SonataAdminBundle'])) {
            $container->prependExtensionConfig('sonata_admin', [
                'templates' => ['user_block' => '@MarlincUser/Admin/Core/user_block.html.twig']
            ]);
        }

        if ($container->hasExtension('twig')) {
            // add custom form widgets
            $container->prependExtensionConfig('twig', ['form_themes' => ['@MarlincUser/Form/form_admin_fields.html.twig']]);
        }
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $bundles = $container->getParameter('kernel.bundles');
        \assert(\is_array($bundles));

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if (isset($bundles['SonataAdminBundle'])) {
            $loader->load('admin.php');
            $loader->load(sprintf('admin_%s.php', $config['manager_type']));
            $loader->load('actions.php');
        }

        $loader->load(sprintf('%s.php', $config['manager_type']));

        $loader->load('twig.php');
        $loader->load('commands.php');
        $loader->load('listener.php');
        $loader->load('mailer.php');
        $loader->load('form.php');
        $loader->load('security.php');
        $loader->load('util.php');

        if (true === $config['security_acl']) {
            $loader->load('security_acl.php');
        }

        $this->checkManagerTypeToModelTypesMapping($config);

        $this->configureClass($config, $container);
        $this->configureMailer($config, $container);
        $this->configureDefaultAvatar($config['profile'], $container);
        if (isset($bundles['SonataAdminBundle'])) {
            $this->configureAdmin($config['admin'], $container);
            $this->configureResetting($config['resetting'], $container);
        }

        if ($this->isConfigEnabled($container, $config['impersonating'])) {
            $this->configureImpersonation($config['impersonating'], $container);
        }
    }

    /**
     * @param array<string, mixed> $config
     */
    private function configureClass(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('marlinc.user.user.class', $config['class']['user']);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function configureAdmin(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('marlinc.user.admin.user.controller', $config['user']['controller']);

        $container->getDefinition('marlinc.user.admin.user')
            ->setClass($config['user']['class'])
            ->addMethodCall('setTranslationDomain', [$config['user']['translation']]);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function configureResetting(array $config, ContainerBuilder $container): void
    {
        $container->getDefinition('marlinc.user.action.request')
            ->replaceArgument(9, $config['retry_ttl']);

        $container->getDefinition('marlinc.user.action.check_email')
            ->replaceArgument(4, $config['token_ttl']);

        $container->getDefinition('marlinc.user.action.reset')
            ->replaceArgument(8, $config['token_ttl']);

        $container->getDefinition('marlinc.user.mailer.default')
            ->replaceArgument(3, [$config['email']['address'] => $config['email']['sender_name']])
            ->replaceArgument(4, $config['email']['template']);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function checkManagerTypeToModelTypesMapping(array $config): void
    {
        $managerType = $config['manager_type'];

        if (!\in_array($managerType, ['orm', 'mongodb'], true)) {
            throw new \InvalidArgumentException(sprintf('Invalid manager type "%s".', $managerType));
        }

    }

    /**
     * @param array<string, mixed> $config
     */
    private function configureMailer(array $config, ContainerBuilder $container): void
    {
        $container->setAlias('marlinc.user.mailer', $config['mailer']);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function configureDefaultAvatar(array $config, ContainerBuilder $container): void
    {
        $container->getDefinition('marlinc.user.twig.global')
            ->replaceArgument(1, $config['default_avatar']);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function configureImpersonation(array $config, ContainerBuilder $container): void
    {
        $container->getDefinition('marlinc.user.twig.global')
            ->replaceArgument(2, $config['enabled'])
            ->replaceArgument(3, $config['route'])
            ->replaceArgument(4, $config['parameters']);
    }
}
