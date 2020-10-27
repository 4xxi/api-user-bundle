<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Security\Core\User\UserInterface;

final class DoctrineResolveUserEntityPass implements CompilerPassInterface
{
    /**
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('doctrine.orm.listeners.resolve_target_entity');

        $definition->addMethodCall('addResolveTargetEntity', [
            UserInterface::class,
            $container->getParameter('api_user.user_class'),
            [],
        ]);

        $definition->addTag('doctrine.event_subscriber', ['connection' => 'default']);
    }
}
