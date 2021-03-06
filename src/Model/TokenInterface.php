<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;

interface TokenInterface
{
    public function getUser(): UserInterface;

    public function getExpiresAt(): \DateTimeImmutable;

    public function getCredentials(): string;

    public function getCreatedAt(): \DateTimeImmutable;
}
