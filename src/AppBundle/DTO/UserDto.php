<?php

namespace AppBundle\DTO;

class UserDto
{
    /** @var string  */
    public $id;
    /** @var bool  */
    public $isDriver;
    /** @var bool  */
    public $isPassenger;
    /** @var string  */
    public $fullName;
    /** @var string  */
    public $username;
    /** @var string  */
    public $email;

    /**
     * @param string $id
     * @param bool $isDriver
     * @param bool $isPassenger
     * @param string $fullName
     * @param string $username
     * @param string $email
     */
    public function __construct(
        string $id,
        bool $isDriver,
        bool $isPassenger,
        string $fullName,
        string $username,
        string $email
    ) {
        $this->id = $id;
        $this->isDriver = $isDriver;
        $this->isPassenger = $isPassenger;
        $this->fullName = $fullName;
        $this->username = $username;
        $this->email = $email;
    }
}
