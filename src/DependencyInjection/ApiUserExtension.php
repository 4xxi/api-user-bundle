<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class ApiUserExtension extends Extension
{
    /**
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $this->loadGeneralConfiguration($config, $container);
        $this->loadTokenConfiguration($config, $container);
        $this->loadTokenAuthConfiguration($config, $container);
        $this->loadLoginConfiguration($config, $container);
        $this->loadRegistrationConfiguration($config, $container);
        $this->loadRegistrationConfirmationConfiguration($config, $container);
        $this->loadMessageSenderConfiguration($config, $container);
        $this->loadFrontendConfiguration($config, $container);
    }

    private function loadGeneralConfiguration(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('api_user.user_class', $config['user_class']);
    }

    private function loadTokenConfiguration(array $config, ContainerBuilder $container): void
    {
        if ($config['token']['use_bundled']) {
            $container->setParameter('api_user.use_bundled_token', true);
        }

        $container->setParameter('api_user.token_lifetime', $config['token']['lifetime']);
        if (null !== $config['token']['credentials_generator']) {
            $container->setAlias('api_user.token_credentials_generator', $config['token']['credentials_generator']);
        }
    }

    private function loadTokenAuthConfiguration(array $config, ContainerBuilder $container): void
    {
        $guard = $container->getDefinition('api_user.token_authenticator');

        $guard->setArgument(0, $config['token_auth']['header']);

        // TODO: validate service interfaces
        if (null !== $config['token_auth']['credentials_provider']) {
            $container->setAlias('api_user.provider.credentials', $config['token_auth']['credentials_provider']);
        }

        if (null !== $config['token_auth']['token_provider']) {
            $tokenProviderId = $config['token_auth']['token_provider'];
            if ('api_user.provider.token' !== $tokenProviderId) {
                $container->setAlias('api_user.provider.token', $tokenProviderId);
            }
        }
    }

    private function loadLoginConfiguration(array $config, ContainerBuilder $container): void
    {
        $loader = $container->getDefinition('api_user.router');
        $loader->setArgument(0, $config['login']['route']);
        $container->setParameter('api_user.allow_unconfirmed_login', $config['login']['allow_unconfirmed_login']);
    }

    private function loadRegistrationConfiguration(array $config, ContainerBuilder $container): void
    {
        if (!$config['registration']['enabled']) {
            return;
        }

        $loader = $container->getDefinition('api_user.router');
        $loader->setArgument(1, $config['registration']['route']);

        if (null !== $config['registration']['form_type']) {
            $container->setAlias('api_user.registration.form_type', $config['registration']['form_type']);
        }
    }

    private function loadRegistrationConfirmationConfiguration(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('api_user.confirmation.frontend_route', '');
        $container->setParameter('api_user.confirmation_enabled', $config['registration']['confirmation']['enabled']);
        if (!$config['registration']['confirmation']['enabled']) {
            return;
        }

        if (empty($config['frontend']['base_url'])) {
            throw new InvalidConfigurationException('Confirmation enabled, but frontend.base_url parameter not set');
        }

        if (empty($config['registration']['confirmation']['frontend_route'])) {
            throw new InvalidConfigurationException('Confirmation enabled, but registration.confirmation.frontend_route parameter not set');
        }

        if (null !== $config['registration']['confirmation']['credentials_generator']) {
            $container->setAlias('api_user.confirmation_token_credentials_generator', $config['registration']['confirmation']['credentials_generator']);
        }

        $loader = $container->getDefinition('api_user.router');
        $loader->setArgument(2, $config['registration']['confirmation']['route']);

        $definition = $container->getDefinition('Fourxxi\ApiUserBundle\EventSubscriber\UserConfirmationSubscriber');
        $definition->addTag('kernel.event_subscriber');

        $container->setParameter('api_user.confirmation.frontend_route', $config['registration']['confirmation']['frontend_route']);
    }

    private function loadMessageSenderConfiguration(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('api_user.message_sender.from', $config['message_sender']['from']);
        $customService = $config['message_sender']['service'];
        $confirmationEnabled = $config['registration']['confirmation']['enabled'];

        if (null === $customService && $confirmationEnabled && empty($config['message_sender']['from']['email'])) {
            throw new InvalidConfigurationException('Confirmation enabled but message_sender.from.email parameter not set');
        }

        if (null !== $customService) {
            $container->setAlias('api_user.message_sender', $customService);
        }
    }

    private function loadFrontendConfiguration(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('api_user.frontend.base_url', rtrim($config['frontend']['base_url'], '/'));
    }
}
