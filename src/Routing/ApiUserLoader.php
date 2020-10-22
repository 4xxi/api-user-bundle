<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Routing;

use Fourxxi\ApiUserBundle\Controller\ConfirmationController;
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
    private $isLoaded = false;

    /**
     * @var string|null
     */
    private $loginRoute;

    /**
     * @var string|null
     */
    private $registrationRoute;

    /**
     * @var string|null
     */
    private $confirmationRoute;

    public function __construct(
        string $loginRoute = null,
        string $registrationRoute = null,
        string $confirmationRoute = null
    ) {
        $this->loginRoute = $loginRoute;
        $this->registrationRoute = $registrationRoute;
        $this->confirmationRoute = $confirmationRoute;
    }

    public function load($resource, $type = null): RouteCollection
    {
        if ($this->isLoaded) {
            throw new \RuntimeException('Do not add the "api_user" loader twice');
        }

        $routes = new RouteCollection();

        if (null !== $this->loginRoute) {
            $loginRoute = new Route($this->loginRoute, [], [], [], null, [], Request::METHOD_POST);
            $routes->add('api_user_login', $loginRoute);
        }

        if (null !== $this->registrationRoute) {
            $routes->add('api_user_registration', new Route($this->registrationRoute, [
                '_controller' => RegistrationController::class,
            ], [], [], null, [], Request::METHOD_POST));
        }

        if (null !== $this->confirmationRoute) {
            $routes->add('api_user_registration_confirmation', new Route($this->confirmationRoute, [
                '_controller' => ConfirmationController::class,
                'token' => null,
            ], [
                'token' => '.+',
            ], [], null, [], Request::METHOD_POST));
        }

        $this->isLoaded = true;

        return $routes;
    }

    public function supports($resource, $type = null): bool
    {
        return 'api_user' === $type;
    }
}
