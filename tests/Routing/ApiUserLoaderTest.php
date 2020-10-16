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
}
