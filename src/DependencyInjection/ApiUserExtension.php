<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class ApiUserExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $this->loadGeneralConfiguration($config, $container);
        $this->loadTokenAuthConfiguration($config, $container);
        $this->loadLoginConfiguration($config, $container);
    }

    private function loadGeneralConfiguration(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('api_user.user_class', $config['user_class']);

        if ($config['use_bundled_token']) {
            $container->setParameter('api_user.use_bundled_token', true);
        }
    }

    private function loadTokenAuthConfiguration(array $config, ContainerBuilder $container): void
    {
        $guard = $container->getDefinition('api_user.token_authenticator');

        $guard->setArgument(0, $config['token_auth']['header']);
        $guard->setArgument(1, $config['token_auth']['check_query_string']);

        // TODO: validate service interfaces
        if (null !== $config['token_auth']['credentials_provider']) {
            $container->setAlias('api_user.provider.credentials', $config['token_auth']['credentials_provider']);
        }

        if (null !== $config['token_auth']['user_provider']) {
            $container->setAlias('api_user.provider.user', $config['token_auth']['user_provider']);
        }
    }

    private function loadLoginConfiguration(array $config, ContainerBuilder $container): void
    {
        $loader = $container->getDefinition('api_user.router');
        $loader->setArgument(0, $config['login']['route']);
    }
}