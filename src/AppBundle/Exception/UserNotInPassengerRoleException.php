<?php

namespace AppBundle\Exception;

use Throwable;

class UserNotInPassengerRoleException extends \Exception
{
    const MESSAGE = 'This user needs to become a registered passenger to perform this action.';

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct(self::MESSAGE, 500, $previous);
    }
}
