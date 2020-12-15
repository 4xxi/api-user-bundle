<?php

namespace Fourxxi\ApiUserBundle\Tests\DependencyInjection;

use Fourxxi\ApiUserBundle\DependencyInjection\ApiUserExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Security\Core\User\User;

final class ApiUserExtensionTest extends TestCase
{
    /**
     * Hard to mock.
     *
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /** @var ApiUserExtension */
    private $apiUserExtension;

    protected function setUp(): void
    {
        $this->containerBuilder = new ContainerBuilder();
        $this->apiUserExtension = new ApiUserExtension();
    }

    /**
     * @test
     */
    public function onlyRequiredParameters(): void
    {
        $this->apiUserExtension->load(['api_user' => [
            'user_class' => User::class,
            'token' => ['use_bundled' => true],
            'token_auth' => ['header' => 'x-api-test'],
            'login' => ['route' => 'test'],
            'registration' => ['confirmation' => ['enabled' => false]],
            'message_sender' => ['from' => []],
            'frontend' => ['base_url' => ''],
        ]], $this->containerBuilder);

        $this->assertTrue($this->containerBuilder->hasParameter('api_user.user_class'));
        $this->assertTrue($this->containerBuilder->hasParameter('api_user.message_sender.from'));
        $this->assertTrue($this->containerBuilder->hasParameter('api_user.confirmation_enabled'));
    }
}
