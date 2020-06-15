<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Provider;

use Fourxxi\ApiUserBundle\Entity\TokenInterface;

interface ApiTokenProviderInterface
{
    public function findTokenByCredentials(string $credentials): ?TokenInterface;
}