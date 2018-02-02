<?php

namespace AppBundle\Exception;

use Throwable;

class RideLifeCycleException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct('Invalid request for this ride.', 500, null);
    }
}
