<?php


namespace Tests\AppBundle\User;

use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;

class FakeUserRepository extends UserRepository
{
    public function saveNewUser(AppUser $passedUser)
    {
        $this->save($passedUser);
        return $passedUser;
    }
}
