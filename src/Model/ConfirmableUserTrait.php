<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Model;

use Doctrine\ORM\Mapping as ORM;

trait ConfirmableUserTrait
{
    /**
     * @var string|null
     * @ORM\Column(nullable=true)
     */
    private $confirmationToken;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $confirmed = false;

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(string $token): void
    {
        $this->confirmationToken = $token;
    }

    public function confirmed(): bool
    {
        return $this->confirmed;
    }

    public function confirm(): void
    {
        $this->confirmed = true;
    }
}
