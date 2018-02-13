<?php

namespace AppBundle\Exception;

class DuplicateRoleAssignmentException extends \Exception
{
    const MESSAGE = 'This role was already assigned to this user.';

    public function __construct()
    {
        parent::__construct(self::MESSAGE, 500, null);
    }
}
