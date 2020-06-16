<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

interface TokenInterface
{
    public function getUser(): UserInterface;

    public function getExpiresAt(): \DateTimeImmutable;

    public function getCredentials(): string;

    public function getCreatedAt(): \DateTimeImmutable;
}