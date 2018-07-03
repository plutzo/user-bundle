<?php
/**
 * Created by PhpStorm.
 * User: elias
 * Date: 14.07.16
 * Time: 13:39
 */

namespace Marlinc\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class MarlincUserExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container): void
    {
        if ($container->hasExtension('twig')) {
            // add custom form widgets
            $container->prependExtensionConfig('twig', ['form_themes' => ['@MarlincUser/Form/form_admin_fields.html.twig']]);
        }

        if ($container->hasExtension('sonata_admin')) {
            $container->prependExtensionConfig('sonata_admin', [
                'templates' => [
                    'user_block' => '@MarlincUser/Admin/Core/user_block.html.twig',
                ],
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);
        $config = $this->fixImpersonating($config);

        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.xml');
        $loader->load('admin.xml');

        if (class_exists('Google\Authenticator\GoogleAuthenticator')) {
            $loader->load('google_authenticator.xml');
        }

        $container->setParameter('marlinc.user.default_avatar', $config['profile']['default_avatar']);
        $container->setParameter('marlinc.user.impersonating', $config['impersonating']);

        $this->configureGoogleAuthenticator($config, $container);
    }

    /**
     * @param array $config
     * @throws \RuntimeException
     * @return array
     */
    public function fixImpersonating(array $config)
    {
        if (isset($config['impersonating'], $config['impersonating_route'])) {
            throw new \RuntimeException('you can\'t have `impersonating` and `impersonating_route` keys defined at the same time');
        }

        if (isset($config['impersonating_route'])) {
            $config['impersonating'] = [
                'route' => $config['impersonating_route'],
                'parameters' => [],
            ];
        }

        if (!isset($config['impersonating']['parameters'])) {
            $config['impersonating']['parameters'] = [];
        }

        if (!isset($config['impersonating']['route'])) {
            $config['impersonating'] = false;
        }

        return $config;
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     * @throws \RuntimeException
     * @return mixed
     */
    public function configureGoogleAuthenticator($config, ContainerBuilder $container)
    {
        $container->setParameter('marlinc.user.google.authenticator.enabled', $config['google_authenticator']['enabled']);

        if (!$config['google_authenticator']['enabled']) {
            $container->removeDefinition('marlinc.user.google.authenticator');
            $container->removeDefinition('marlinc.user.google.authenticator.provider');
            $container->removeDefinition('marlinc.user.google.authenticator.interactive_login_listener');
            $container->removeDefinition('marlinc.user.google.authenticator.request_listener');

            return;
        }

        if (!class_exists('Google\Authenticator\GoogleAuthenticator')) {
            throw new \RuntimeException('Please add ``sonata-project/google-authenticator`` package');
        }

        $container->setParameter('marlinc.user.google.authenticator.forced_for_role', $config['google_authenticator']['forced_for_role']);
        $container->setParameter('marlinc.user.google.authenticator.ip_white_list', $config['google_authenticator']['ip_white_list']);

        $container->getDefinition('marlinc.user.google.authenticator.provider')
            ->replaceArgument(0, $config['google_authenticator']['server']);
    }
}
