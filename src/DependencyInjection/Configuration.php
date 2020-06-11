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
        $this->buildApiGuardConfiguration($treeBuilder);

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
                ->booleanNode('use_bundled_token')
                    ->defaultTrue()
                ->end()
            ->end()
        ;
    }

    private function buildApiGuardConfiguration(TreeBuilder $builder): void
    {
        $root = $builder->getRootNode();
        $root
            ->children()
                ->arrayNode('api_guard')
                    ->children()
                        ->scalarNode('header')->defaultValue('X-API-TOKEN')->end()
                        ->booleanNode('check_query_string')->defaultFalse()->end()
                        ->scalarNode('credentials_provider')->defaultNull()->end()
                        ->scalarNode('user_provider')->defaultNull()->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}