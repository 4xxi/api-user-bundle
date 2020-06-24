<?php

declare(strict_types=1);

namespace Fourxxi\ApiUserBundle\Security\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

final class UserNotConfirmedException extends AccountStatusException
{
}
