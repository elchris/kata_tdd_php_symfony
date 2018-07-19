<?php

namespace AppBundle\Dto;

class UserDto
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $first;
    /**
     * @var string
     */
    private $last;
    /**
     * @var array
     */
    private $roles;

    /**
     * UserDto constructor.
     * @param string $id
     * @param string $first
     * @param string $last
     * @param array $roles
     */
    public function __construct(string $id, string $first, string $last, array $roles)
    {
        $this->id = $id;
        $this->first = $first;
        $this->last = $last;
        $this->roles = $roles;
    }
}
