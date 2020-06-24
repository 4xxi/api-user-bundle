<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Routing;

use Fourxxi\ApiUserBundle\Controller\RegistrationController;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class ApiUserLoader extends Loader
{
    /**
     * @var bool
     */
    private $loaded;

    /**
     * @var string|null
     */
    private $loginRoute;

    /**
     * @var string|null
     */
    private $registrationRoute;

    public function __construct(string $loginRoute = null, string $registrationRoute = null)
    {
        $this->loaded = false;
        $this->loginRoute = $loginRoute;
        $this->registrationRoute = $registrationRoute;
    }

    public function load($resource, $type = null): RouteCollection
    {
        if ($this->loaded) {
            throw new \RuntimeException('Do not add the "api_user" loader twice');
        }

        $routes = new RouteCollection();

        if (null !== $this->loginRoute) {
            $routes->add('api_user_login', new Route($this->loginRoute));
        }

        if (null !== $this->registrationRoute) {
            $routes->add('api_user_registration', new Route($this->registrationRoute, [
                '_controller' => RegistrationController::class.'::register',
            ], [], [], null, [], Request::METHOD_POST));
        }

        return $routes;
    }

    public function supports($resource, $type = null): bool
    {
        return 'api_user' === $type;
    }
}
