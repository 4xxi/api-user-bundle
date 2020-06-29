<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Sender;

use Symfony\Component\Security\Core\User\UserInterface;

interface MessageSenderInterface
{
    public function sendConfirmationMessage(UserInterface $user, string $confirmationUrl): void;
}
