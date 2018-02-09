<?php

namespace AppBundle\DTO;

class UserDto
{
    public $id;
    public $isDriver;
    public $isPassenger;
    public $fullName;

    /**
     * @param string $id
     * @param bool $isDriver
     * @param bool $isPassenger
     * @param string $fullName
     */
    public function __construct(
        string $id,
        bool $isDriver,
        bool $isPassenger,
        string $fullName
    ) {
        $this->id = $id;
        $this->isDriver = $isDriver;
        $this->isPassenger = $isPassenger;
        $this->fullName = $fullName;
    }
}
