<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;

interface UserRepositoryInterface
{
    public function newUser($firstName, $lastName);

    /**
     * @param integer $userId
     * @return AppUser
     */
    public function getUserById($userId);

    public function assignRoleToUser(AppUser $user, AppRole $role);

    public function isUserInRole(AppUser $user, AppRole $role);
}