<?php

namespace AppBundle\Exception;

use Throwable;

class ActingDriverIsNotAssignedDriverException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct('The driver perpetrating this action is not the driver assigned to this ride.', 500, null);
    }
}
