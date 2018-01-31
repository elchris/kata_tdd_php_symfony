<?php


namespace AppBundle\Exception;

use Throwable;

class UserNotFoundException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct('The user requested could not be found.', 404, $previous);
    }
}
