<?php

namespace AppBundle\Exception;

use Throwable;

class UserNotInPassengerRoleException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct('This user needs to become a registered passenger to perform this action.', 500, $previous);
    }
}
