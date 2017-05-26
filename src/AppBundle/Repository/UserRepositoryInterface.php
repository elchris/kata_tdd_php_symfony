<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;

interface UserRepositoryInterface
{
    public function getDriverRole();

    public function makeUserPassenger(AppUser $user);

    public function makeUserDriver(AppUser $user);

    public function saveUser(AppUser $user);

    /**
     * @param int $userId
     * @return AppUser
     */
    public function getUserById($userId);

    public function isUserPassenger(AppUser $user);

    public function isUserDriver(AppUser $user);

    /**
     * @param AppUser $user
     * @return Ride[]
     */
    public function getRidesForUser(Appuser $user);

    /**
     * @return AppLocation[]
     */
    public function getAllLocations();
}