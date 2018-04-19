<?php


namespace AppBundle\DTO;

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
     * @var array $roles
     */
    private $roles;

    public function __construct(string $id, string $first, string $last, array $roles)
    {
        $this->id = $id;
        $this->first = $first;
        $this->last = $last;
        $this->roles = $roles;
    }
}
