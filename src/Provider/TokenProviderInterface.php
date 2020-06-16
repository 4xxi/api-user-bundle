<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Provider;

use Fourxxi\ApiUserBundle\Entity\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface TokenProviderInterface
{
    public function findTokenByCredentials(string $credentials): ?TokenInterface;

    public function createTokenForUser(UserInterface $user): TokenInterface;
}