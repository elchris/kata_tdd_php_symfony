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

    /**
     * @param $firstName
     * @param $lastName
     * @param $email
     * @param $username
     * @param $password
     * @return AppUser
     */
    public function newUser($firstName, $lastName, $email, $username, $password)
    {
        $newUser = new AppUser($firstName, $lastName, $email, $username, $password);
        return $this->userRepository->saveNewUser($newUser);
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
        return $user->userHasRole(AppRole::driver());
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
        return $user->userHasRole(AppRole::passenger());
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
