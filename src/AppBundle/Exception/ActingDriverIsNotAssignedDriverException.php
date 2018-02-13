<?php

namespace AppBundle\Exception;

use Throwable;

class ActingDriverIsNotAssignedDriverException extends \Exception
{
    const MESSAGE = 'The driver perpetrating this action is not the driver assigned to this ride.';

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct(self::MESSAGE, 500, null);
    }
}
