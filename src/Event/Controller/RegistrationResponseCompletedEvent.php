<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Event\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class RegistrationResponseCompletedEvent extends Event
{
    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @var Response|null
     */
    private $response;

    public function __construct(UserInterface $user, Response $response = null)
    {
        $this->user = $user;
        $this->response = $response;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
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
