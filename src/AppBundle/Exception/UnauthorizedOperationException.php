<?php

namespace AppBundle\Exception;

use Throwable;

class UnauthorizedOperationException extends \Exception
{
    const MESSAGE = 'You are not authorized to perform this operation';

    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct(self::MESSAGE, 403, $previous);
    }
}
