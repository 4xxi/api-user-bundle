<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Model;

interface ConfirmableUserInterface
{
    public function getConfirmationToken(): ?string;

    public function setConfirmationToken(string $token): void;

    public function confirmed(): bool;

    public function confirm(): void;
}
