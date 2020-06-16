<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Event\Security\Guard;

use Fourxxi\ApiUserBundle\Entity\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

final class LoginAuthenticationSuccessEvent extends Event
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var TokenInterface
     */
    private $token;

    /**
     * @var Response|null
     */
    private $response;

    public function __construct(Request $request, TokenInterface $token, Response $response = null)
    {
        $this->request = $request;
        $this->token = $token;
        $this->response = $response;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getToken(): TokenInterface
    {
        return $this->token;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function setResponse(?Response $response): void
    {
        $this->response = $response;
    }
}
