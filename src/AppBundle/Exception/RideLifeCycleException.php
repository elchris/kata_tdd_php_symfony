<?php

namespace AppBundle\Exception;

use Throwable;

class RideLifeCycleException extends \Exception
{
    const MESSAGE = 'Invalid request for this ride.';

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct(self::MESSAGE, 500, null);
    }
}
