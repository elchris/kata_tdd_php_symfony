<?php

namespace AppBundle\Service;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Exception\DuplicateRoleAssignmentException;
use AppBundle\Exception\UnauthorizedOperationException;
use AppBundle\Exception\UserNotFoundException;
use AppBundle\Repository\UserRepositoryInterface;
use Ramsey\Uuid\Uuid;

class UserService
{
    /** @var UserRepositoryInterface $userRepository */
    private $userRepository;

    /** @var AppUser $authenticatedUser */
    private $authenticatedUser;

    /**
     * UserService constructor.
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function setAuthenticatedUser(AppUser $user)
    {
        $this->authenticatedUser = $user;
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
     * @throws UnauthorizedOperationException
     */
    public function getUserById(Uuid $userId)
    {
        $this->verifyAuthenticatedId($userId);
        return $this->userRepository->getUserById($userId);
    }

    /**
     * @param AppUser $user
     * @throws DuplicateRoleAssignmentException
     * @throws UnauthorizedOperationException
     */
    public function makeUserDriver(AppUser $user)
    {
        $this->assignRole($user, AppRole::driver());
    }

    /**
     * @param AppUser $user
     * @throws DuplicateRoleAssignmentException
     * @throws UnauthorizedOperationException
     */
    public function makeUserPassenger(AppUser $user)
    {
        $this->assignRole($user, AppRole::passenger());
    }

    /**
     * @param AppUser $user
     * @param AppRole $role
     * @throws DuplicateRoleAssignmentException
     * @throws UnauthorizedOperationException
     */
    private function assignRole(AppUser $user, AppRole $role)
    {
        $this->verifyAuthenticatedUser($user);
        $this->userRepository->assignRoleToUser($user, $role);
    }

    /**
     * @param Uuid $userId
     * @throws UnauthorizedOperationException
     */
    private function verifyAuthenticatedId(Uuid $userId): void
    {
        if (!$this->authenticatedUser->getId()->equals($userId)) {
            throw new UnauthorizedOperationException();
        }
    }

    /**
     * @param AppUser $user
     * @throws UnauthorizedOperationException
     */
    private function verifyAuthenticatedUser(AppUser $user): void
    {
        if (!$user->is($this->authenticatedUser)) {
            throw new UnauthorizedOperationException();
        }
    }
}
