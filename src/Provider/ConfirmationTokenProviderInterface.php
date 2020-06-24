<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Provider;

use Fourxxi\ApiUserBundle\Entity\ConfirmationTokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface ConfirmationTokenProviderInterface
{
    public function findTokenByCredentials(string $credentials): ?ConfirmationTokenInterface;

    public function createTokenForUser(UserInterface $user): ConfirmationTokenInterface;
}
