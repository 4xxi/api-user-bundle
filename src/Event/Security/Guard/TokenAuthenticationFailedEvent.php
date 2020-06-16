<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Event\Security\Guard;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Contracts\EventDispatcher\Event;

final class TokenAuthenticationFailedEvent extends Event
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var AuthenticationException
     */
    private $exception;

    /**
     * @var Response|null
     */
    private $response;

    public function __construct(Request $request, AuthenticationException $exception, Response $response = null)
    {
        $this->request = $request;
        $this->exception = $exception;
        $this->response = $response;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getException(): AuthenticationException
    {
        return $this->exception;
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
