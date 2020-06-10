<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Provider\Security\Guard;

interface CredentialsProviderInterface
{
    public function getCredentials(string $rawCredentials): string;

    public function isValid(string $rawCredentials): bool;
}