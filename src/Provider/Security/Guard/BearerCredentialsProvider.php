<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Provider\Security\Guard;

final class BearerCredentialsProvider implements CredentialsProviderInterface
{
    private const PATTERN = '/^Bearer (.*?)$/';

    public function getCredentials(string $rawCredentials): string
    {
        if (!preg_match(self::PATTERN, $rawCredentials, $matches)) {
            throw new \UnexpectedValueException();
        }

        return $matches[1];
    }

    public function isValid(string $rawCredentials): bool
    {
        return (bool) preg_match(self::PATTERN, $rawCredentials);
    }
}