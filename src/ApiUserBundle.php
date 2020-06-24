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
        $this->registerDoctrineTokenPass($container);
        $this->registerDoctrineConfirmationTokenPass($container);
    }

    private function registerDoctrineTokenPass(ContainerBuilder $container): void
    {
        $mappings = [
            realpath(__DIR__.'/Resources/config/doctrine-mappings/token') => 'Fourxxi\ApiUserBundle\Entity',
        ];

        $mappingPass = DoctrineOrmMappingsPass::createXmlMappingDriver(
            $mappings,
            [],
            'api_user.use_bundled_token'
        );

        $container->addCompilerPass($mappingPass);
    }

    private function registerDoctrineConfirmationTokenPass(ContainerBuilder $container): void
    {
        $mappings = [
            realpath(__DIR__.'/Resources/config/doctrine-mappings/confirmation-token') => 'Fourxxi\ApiUserBundle\Entity',
        ];

        $mappingPass = DoctrineOrmMappingsPass::createXmlMappingDriver(
            $mappings,
            [],
            'api_user.use_bundled_confirmation_token'
        );

        $container->addCompilerPass($mappingPass);
    }
}
