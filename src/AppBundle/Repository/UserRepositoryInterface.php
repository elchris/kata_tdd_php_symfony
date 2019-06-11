<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Exception\MissingRoleException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Ramsey\Uuid\Uuid;

interface UserRepositoryInterface
{
    public function saveAndGet(AppUser $user): AppUser;

    /**
     * @param Uuid $id
     * @return AppUser
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function byId(Uuid $id): AppUser;

    /**
     * @param AppRole $roleToFind
     * @return AppRole
     * @throws MissingRoleException
     * @throws NonUniqueResultException
     */
    public function getRoleReference(AppRole $roleToFind): AppRole;
}
