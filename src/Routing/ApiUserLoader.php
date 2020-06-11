<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class ApiUserLoader extends Loader
{
    /**
     * @var bool
     */
    private $loaded;

    /**
     * @var string
     */
    private $loginRoute;

    public function __construct(string $loginRoute)
    {
        $this->loaded = false;
        $this->loginRoute = $loginRoute;
    }

    public function load($resource, $type = null): RouteCollection
    {
        if ($this->loaded) {
            throw new \RuntimeException('Do not add the "api_user" loader twice');
        }

        $routes = new RouteCollection();
        $routes->add('api_user_login', new Route($this->loginRoute));

        return $routes;
    }

    public function supports($resource, $type = null): bool
    {
        return 'api_user' === $type;
    }
}