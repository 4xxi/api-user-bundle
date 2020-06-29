<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Service;

final class FrontendRouter implements FrontendRouterInterface
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $confirmationRoute;

    public function __construct(string $baseUrl, string $confirmationRoute)
    {
        $this->baseUrl = $baseUrl;
        $this->confirmationRoute = $confirmationRoute;
    }

    public function getConfirmationRoute(string $token): string
    {
        return sprintf('%s/%s', $this->baseUrl, str_replace('{token}', $token, ltrim($this->confirmationRoute, '/')));
    }
}
