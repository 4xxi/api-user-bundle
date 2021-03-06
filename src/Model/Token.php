<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;

class Token implements TokenInterface
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
     * @var \DateTimeImmutable
     */
    private $createdAt;

    /**
     * @var \DateTimeImmutable
     */
    private $expiresAt;

    /**
     * @var string
     */
    private $credentials;

    public function __construct(UserInterface $user, \DateTimeImmutable $expiresAt, string $credentials)
    {
        $this->user = $user;
        $this->expiresAt = $expiresAt;
        $this->credentials = $credentials;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function getCredentials(): string
    {
        return $this->credentials;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
