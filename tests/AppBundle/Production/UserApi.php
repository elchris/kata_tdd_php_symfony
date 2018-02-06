<?php


namespace Tests\AppBundle\Production;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Exception\DuplicateRoleAssignmentException;
use AppBundle\Exception\UserNotFoundException;
use AppBundle\Repository\UserRepository;
use AppBundle\Repository\UserRepositoryInterface;
use AppBundle\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;

class UserApi
{
    /** @var UserService */
    private $userService;

    /** @var UserRepository */
    private $userRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->userRepository = new UserRepository($entityManager);
        $this->userService = new UserService(
            $this->userRepository
        );
        $this->entityManager = $entityManager;
    }

    /**
     * @return UserRepositoryInterface
     */
    public function getRepo()
    {
        return $this->userRepository;
    }

    /**
     * @param $userId
     * @return AppUser
     * @throws UserNotFoundException
     */
    public function getServiceUserById($userId)
    {
        return $this->userService->getUserById($userId);
    }

    public function getSavedUserWithName($first, $last)
    {
        return $this->userService->newUser($first, $last);
    }

    /**
     * @return AppUser
     */
    public function getSavedUser()
    {
        return $this->getSavedUserWithName('chris', 'holland');
    }

    /**
     * @return AppUser
     * @throws DuplicateRoleAssignmentException
     */
    public function getNewPassenger()
    {
        $passenger = $this->getSavedUser();
        $this->makeUserPassenger($this->getSavedUser());
        return $passenger;
    }

    /**
     * @param AppUser $user
     * @throws DuplicateRoleAssignmentException
     */
    public function makeUserPassenger(AppUser $user)
    {
        $this->userService->makeUserPassenger($user);
    }

    /**
     * @return AppUser
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     */
    public function getSavedPassenger(): AppUser
    {
        $ridePassenger = $this->getNewPassenger();
        $savedPassenger = $this->getServiceUserById($ridePassenger->getId());

        return $savedPassenger;
    }

    /**
     * @param AppUser $driver
     * @throws DuplicateRoleAssignmentException
     */
    public function makeUserDriver(AppUser $driver)
    {
        $this->userService->makeUserDriver($driver);
    }

    /**
     * @param $user
     * @return bool
     */
    public function isPassenger(AppUser $user)
    {
        return $this->userService->isPassenger($user);
    }

    /**
     * @param AppUser $user
     * @return bool
     */
    public function isDriver(AppUser $user)
    {
        return $this->userService->isDriver($user);
    }

    /**
     * @return AppUser
     * @throws DuplicateRoleAssignmentException
     */
    public function getNewDriver()
    {
        return $this->getNewDriverWithName('new', 'driver');
    }

    /**
     * @param $first
     * @param $last
     * @return AppUser
     * @throws DuplicateRoleAssignmentException
     */
    public function getNewDriverWithName($first, $last)
    {
        $driver = $this->getSavedUserWithName($first, $last);
        $this->makeUserDriver($driver);
        return $driver;
    }

    private function saveRole(AppRole $role)
    {
        $this->entityManager->persist($role);
        $this->entityManager->flush();
    }

    public function bootStrapRoles()
    {
        $this->saveRole(AppRole::driver());
        $this->saveRole(AppRole::passenger());
    }
}
