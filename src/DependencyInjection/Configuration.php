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
        $root = $treeBuilder->getRootNode();

        $root
            ->children()
                ->scalarNode('user_class')
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifTrue(function ($class) { return !class_exists($class); })
                        ->thenInvalid('Provided user class "%s" does not exist.')
                    ->end()
                ->end()
                ->booleanNode('use_bundled_token')->defaultTrue()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}