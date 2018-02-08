<?php

namespace AppBundle\DTO;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;

class UserDto
{
    public $id;
    public $isDriver;
    public $isPassenger;
    public $fullName;

    /**
     * @param AppUser $createdUser
     */
    public function __construct(AppUser $createdUser)
    {
        $this->id = $createdUser->getId()->toString();
        $this->isDriver = $createdUser->hasRole(AppRole::driver());
        $this->isPassenger = $createdUser->hasRole(AppRole::passenger());
        $this->fullName = trim($createdUser->getFirstName().' '.$createdUser->getLastName());
    }
}
