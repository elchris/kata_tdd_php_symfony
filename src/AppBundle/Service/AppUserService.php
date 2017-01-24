<?php


namespace AppBundle\Service;

use AppBundle\Repository\AppUserDao;
use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;

class AppUserService
{
    /**
     * @var AppUserDao
     */
    private $userDao;

    /**
     * @param AppUserDao $dao
     */
    public function __construct(AppUserDao $dao)
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
