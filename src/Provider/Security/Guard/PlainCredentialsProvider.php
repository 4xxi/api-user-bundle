<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Provider\Security\Guard;

final class PlainCredentialsProvider implements CredentialsProviderInterface
{
    public function getCredentials(string $rawCredentials): string
    {
        return $rawCredentials;
    }

    public function isValid(string $rawCredentials): bool
    {
        return true;
    }
}
