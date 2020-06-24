<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

interface ConfirmationTokenInterface
{
    public function getUser(): UserInterface;

    public function getCredentials(): string;
}
