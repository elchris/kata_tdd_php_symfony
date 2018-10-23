<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Exception\DuplicateRoleAssignmentException;
use AppBundle\Exception\UserNotFoundException;
use Ramsey\Uuid\Uuid;

interface UserRepositoryInterface
{
    /**
     * @param Uuid $userId
     * @return AppUser
     * @throws UserNotFoundException
     */
    public function getUserById(Uuid $userId): AppUser;

    /**
     * @param AppUser $user
     * @param AppRole $role
     * @throws DuplicateRoleAssignmentException
     */
    public function assignRoleToUser(AppUser $user, AppRole $role): void;

    /**
     * @param AppUser $passedUser
     * @return AppUser
     */
    public function saveNewUser(AppUser $passedUser): AppUser;
}
