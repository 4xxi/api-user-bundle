<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Security;

use Fourxxi\ApiUserBundle\Model\ConfirmableUserInterface;
use Fourxxi\ApiUserBundle\Security\Exception\UserNotConfirmedException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserChecker implements UserCheckerInterface
{
    /**
     * @var bool
     */
    private $confirmationEnabled;

    /**
     * @var bool
     */
    private $allowUnconfirmedLogin;

    public function __construct(
        bool $confirmationEnabled,
        bool $allowUnconfirmedLogin
    ) {
        $this->confirmationEnabled = $confirmationEnabled;
        $this->allowUnconfirmedLogin = $allowUnconfirmedLogin;
    }

    public function checkPreAuth(UserInterface $user)
    {
        if ($this->confirmationEnabled && !$this->allowUnconfirmedLogin) {
            if (!is_subclass_of($user, ConfirmableUserInterface::class)) {
                return;
            }

            if (!$user->confirmed()) {
                $exception = new UserNotConfirmedException();
                $exception->setUser($user);

                throw $exception;
            }
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
    }
}
