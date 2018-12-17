<?php


namespace AppBundle\Entity;

class UserDto
{
    /**
     * @var string
     */
    private $first;
    /**
     * @var string
     */
    private $last;
    /**
     * @var string
     */
    private $id;
    /**
     * @var array
     */
    private $roles;

    /**
     * @param string $id
     * @param string $firstName
     * @param string $lastName
     */
    public function __construct(string $id, string $firstName, string $lastName, array $roles)
    {
        $this->first = $firstName;
        $this->last = $lastName;
        $this->id = $id;
        $this->roles = $roles;
    }
}
