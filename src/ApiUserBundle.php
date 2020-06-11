<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Fourxxi\ApiUserBundle\DependencyInjection\Compiler\DoctrineResolveUserEntityPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ApiUserBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new DoctrineResolveUserEntityPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 1);
        $this->registerDoctrinePass($container);
    }

    private function registerDoctrinePass(ContainerBuilder $container): void
    {
        $mappings = [
            realpath(__DIR__ . '/Resources/config/doctrine-mappings') => 'Fourxxi\ApiUserBundle\Entity',
        ];

        $mappingPass = DoctrineOrmMappingsPass::createYamlMappingDriver(
            $mappings,
            [],
            'api_user.use_bundled_token'
        );

        $container->addCompilerPass($mappingPass);
    }
}