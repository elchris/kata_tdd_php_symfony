<?php

namespace AppBundle\Exception;

class DuplicateRoleAssignmentException extends \Exception
{
    public function __construct()
    {
        parent::__construct('This role was already assigned to this user.', 500, null);
    }
}
