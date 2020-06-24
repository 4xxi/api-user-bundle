<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('api_user');

        $this->buildGeneralConfiguration($treeBuilder);
        $this->buildTokenConfiguration($treeBuilder);
        $this->buildTokenGuardConfiguration($treeBuilder);
        $this->buildLoginConfiguration($treeBuilder);
        $this->buildRegistrationConfiguration($treeBuilder);

        return $treeBuilder;
    }

    private function buildGeneralConfiguration(TreeBuilder $builder): void
    {
        $root = $builder->getRootNode();
        $root
            ->children()
                ->scalarNode('user_class')
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifTrue(function ($class) { return !class_exists($class); })
                        ->thenInvalid('Provided user class "%s" does not exist.')
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function buildTokenConfiguration(TreeBuilder $builder): void
    {
        $root = $builder->getRootNode();
        $root
            ->children()
                ->arrayNode('token')
                    ->children()
                        ->booleanNode('use_bundled')->defaultTrue()->end()
                        ->integerNode('lifetime')->defaultValue(86400)->end()
                        ->scalarNode('credentials_generator')->defaultNull()->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function buildTokenGuardConfiguration(TreeBuilder $builder): void
    {
        $root = $builder->getRootNode();
        $root
            ->children()
                ->arrayNode('token_auth')
                    ->children()
                        ->scalarNode('header')->defaultValue('X-API-TOKEN')->end()
                        ->booleanNode('check_query_string')->defaultFalse()->end()
                        ->scalarNode('credentials_provider')->defaultNull()->end()
                        ->scalarNode('token_provider')->defaultNull()->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function buildLoginConfiguration(TreeBuilder $builder): void
    {
        $root = $builder->getRootNode();
        $root
            ->children()
                ->arrayNode('login')
                    ->children()
                        ->scalarNode('route')->defaultValue('/api/login')->end()
                        ->booleanNode('allow_unconfirmed_login')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function buildRegistrationConfiguration(TreeBuilder $builder): void
    {
        $root = $builder->getRootNode();
        $root
            ->children()
                ->arrayNode('registration')
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->scalarNode('route')->defaultValue('/api/register')->end()
                        ->scalarNode('form_type')->defaultNull()->end()
                        ->arrayNode('confirmation')
                            ->children()
                                ->booleanNode('enabled')->defaultFalse()->end()
                                ->scalarNode('credentials_generator')->defaultNull()->end()
                                ->scalarNode('token_provider')->defaultNull()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
