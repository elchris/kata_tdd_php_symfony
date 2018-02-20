<?php
namespace AppBundle\Exception;

use Throwable;

class RideNotFoundException extends \Exception
{
    const MESSAGE = 'The ride requested could not be found.';

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct(self::MESSAGE, 404, $previous);
    }
}
