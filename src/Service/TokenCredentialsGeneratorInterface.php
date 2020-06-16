<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Service;

interface TokenCredentialsGeneratorInterface
{
    public function generate(): string;
}