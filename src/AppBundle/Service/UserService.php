<?php

namespace AppBundle\Service;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;
use Ramsey\Uuid\Uuid;

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
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function newUser($firstName, $lastName)
    {
        $newUser = new AppUser($firstName, $lastName);
        $this->userRepository->save($newUser);
        return $newUser;
    }

    /**
     * @param Uuid $userId
     * @return AppUser
     */
    public function getUserById(Uuid $userId)
    {
        return $this->userRepository->getUserById($userId);
    }

    public function makeUserDriver(AppUser $user)
    {
        $this->assignRole($user, AppRole::driver());
    }

    public function isDriver(AppUser $user)
    {
        return $user->hasRole(AppRole::driver());
    }

    public function makeUserPassenger(AppUser $user)
    {
        $this->assignRole($user, AppRole::passenger());
    }

    public function isPassenger(AppUser $user)
    {
        return $user->hasRole(AppRole::passenger());
    }

    /**
     * @param AppUser $user
     * @param $role
     */
    protected function assignRole(AppUser $user, AppRole $role)
    {
        $this->userRepository->assignRoleToUser($user, $role);
    }
}
