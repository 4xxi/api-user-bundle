<?php

namespace Fourxxi\ApiUserBundle\Tests\Routing;

use Fourxxi\ApiUserBundle\Routing\ApiUserLoader;
use PHPUnit\Framework\TestCase;

final class ApiUserLoaderTest extends TestCase
{
    /**
     * @test
     */
    public function checksIfRoutesWereLoadedTwice(): void
    {
        $this->expectException(\RuntimeException::class);

        $apiUserLoader = new ApiUserLoader();

        $apiUserLoader->load('test');
        $apiUserLoader->load('test');
    }

    /**
     * @test
     */
    public function addsRoutes(): void
    {
        $apiUserLoader = new ApiUserLoader(
            '/expected-login',
            '/expected-registration',
            '/expected-confirmation'
        );

        $routeCollection = $apiUserLoader->load('irrelevant');

        $this->assertEquals('/expected-login', $routeCollection->get('api_user_login')->getPath());
        $this->assertEquals('/expected-registration', $routeCollection->get('api_user_registration')->getPath());
        $this->assertEquals('/expected-confirmation', $routeCollection->get('api_user_registration_confirmation')->getPath());
    }
}
