<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

class ConfirmationToken implements ConfirmationTokenInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @var string
     */
    private $credentials;

    public function __construct(UserInterface $user, string $credentials)
    {
        $this->user = $user;
        $this->credentials = $credentials;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getCredentials(): string
    {
        return $this->credentials;
    }
}
