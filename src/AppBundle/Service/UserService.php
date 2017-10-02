<?php

namespace AppBundle\Service;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;

class UserService
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * UserService constructor.
     * @param UserRepository $userRepository
     */
    public function __construct($userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function newUser($firstName, $lastName)
    {
        $newUser = new AppUser($firstName, $lastName);
        $this->userRepository->save($newUser);
    }

    /**
     * @param int $userId
     * @return AppUser
     */
    public function getUserById($userId)
    {
        return $this->userRepository->getUserById($userId);
    }

    public function makeUserDriver(AppUser $user)
    {
        $this->userRepository->assignRoleToUser($user, AppRole::driver());
    }

    public function isDriver(AppUser $user)
    {
        return $this->userRepository->userHasRole($user, AppRole::driver());
    }
}
