<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Provider;

use Symfony\Component\Security\Core\User\UserInterface;

interface ApiUserProviderInterface
{
    public function findUserByTokenCredentials(string $credentials): ?UserInterface;
}