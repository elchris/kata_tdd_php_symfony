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
    }

    /**
     * @param $userId
     * @return AppUser
     */
    public function getUserById($userId)
    {
        return $this->userRepository->getUserById($userId);
    }

    public function makeDriver(AppUser $user)
    {
        $this->userRepository->assignRoleToUser($user, AppRole::driver());
    }

    public function isDriver(AppUser $user)
    {
        return $this->userRepository->hasRole($user, AppRole::driver());
    }

    public function makePassenger(AppUser $user)
    {
        $this->userRepository->assignRoleToUser($user, AppRole::passenger());
    }

    public function isPassenger(AppUser $user)
    {
        return $this->userRepository->hasRole($user, AppRole::passenger());
    }
}
