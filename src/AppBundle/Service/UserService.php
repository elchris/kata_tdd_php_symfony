<?php

namespace AppBundle\Service;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Exception\DuplicateRoleAssignmentException;
use AppBundle\Exception\UserNotFoundException;
use AppBundle\Repository\UserRepositoryInterface;
use Ramsey\Uuid\Uuid;

class UserService
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * UserService constructor.
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function newUser($firstName, $lastName)
    {
        $newUser = new AppUser($firstName, $lastName);
        $this->userRepository->saveUser($newUser);
        return $newUser;
    }

    /**
     * @param Uuid $userId
     * @return AppUser
     * @throws UserNotFoundException
     */
    public function getUserById(Uuid $userId)
    {
        return $this->userRepository->getUserById($userId);
    }

    /**
     * @param AppUser $user
     * @throws DuplicateRoleAssignmentException
     */
    public function makeUserDriver(AppUser $user)
    {
        $this->assignRole($user, AppRole::driver());
    }

    public function isDriver(AppUser $user)
    {
        return $user->hasRole(AppRole::driver());
    }

    /**
     * @param AppUser $user
     * @throws DuplicateRoleAssignmentException
     */
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
     * @param AppRole $role
     * @throws DuplicateRoleAssignmentException
     */
    private function assignRole(AppUser $user, AppRole $role)
    {
        $this->userRepository->assignRoleToUser($user, $role);
    }
}
