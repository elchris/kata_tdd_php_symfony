<?php

namespace AppBundle\Exception;

use Throwable;

class UserNotInDriverRoleException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct('This user needs to become a registered driver to perform this action.', 500, $previous);
    }
}
