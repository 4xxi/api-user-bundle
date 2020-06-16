<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Service;

final class TokenGenerator implements TokenCredentialsGeneratorInterface
{
    public function generate(): string
    {
        return bin2hex(random_bytes(16));
    }
}