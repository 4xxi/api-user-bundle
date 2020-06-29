<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Service;

interface FrontendRouterInterface
{
    public function getConfirmationRoute(string $token): string;
}
