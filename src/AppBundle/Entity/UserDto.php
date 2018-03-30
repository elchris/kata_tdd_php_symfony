<?php

namespace AppBundle\Entity;

class UserDto
{
    public $first;
    public $last;

    public function __construct($first, $last)
    {

        $this->first = $first;
        $this->last = $last;
    }
}
