<?php


namespace AppBundle\Service;

use AppBundle\Repository\UserRepository;
use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Repository\UserRepositoryInterface;

class AppUserService
{
    /**
     * @var UserRepositoryInterface
     */
    private $userDao;

    /**
     * @param UserRepositoryInterface $dao
     */
    public function __construct(UserRepositoryInterface $dao)
    {
        $this->userDao = $dao;
    }

    public function saveUser(AppUser $user)
    {
        $this->userDao->saveUser($user);
    }

    /**
     * @param int $userId
     * @return AppUser
     */
    public function getUserById($userId)
    {
        return $this->userDao->getUserById($userId);
    }

    /**
     * @param AppUser $savedUser
     */
    public function makeUserPassenger(AppUser $savedUser)
    {
        $this->userDao->makeUserPassenger($savedUser);
    }

    public function isUserPassenger(AppUser $user)
    {
        return $this->userDao->isUserPassenger($user);
    }

    public function makeUserDriver(AppUser $user)
    {
        $this->userDao->makeUserDriver($user);
    }

    public function isUserDriver(AppUser $user)
    {
        return $this->userDao->isUserDriver($user);
    }

    /**
     * @param AppUser $user
     * @return Ride[]
     */
    public function getRidesForUser(AppUser $user)
    {
        return $this->userDao->getRidesForUser($user);
    }

    /**
     * @return AppLocation[]
     */
    public function getAllLocations()
    {
        return $this->userDao->getAllLocations();
    }
}
