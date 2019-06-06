<?php

namespace AppBundle\Exception;

use Exception;

class InvalidRoleException extends Exception
{

    /**
     * InvalidRoleException constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct('Invalid Role Name: '.$name);
    }
}
