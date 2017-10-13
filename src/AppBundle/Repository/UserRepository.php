<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Exception\DuplicateRoleAssignmentException;

class UserRepository extends AppRepository
{
    /**
     * @param $userId
     * @return AppUser
     */
    public function getUserById($userId)
    {
        return $this->em->createQuery(
            'select u from E:AppUser u where u.id = :userId'
        )
        ->setParameter('userId', $userId)
        ->getSingleResult();
    }

    public function assignRoleToUser(AppUser $user, AppRole $role)
    {
        if ($this->userHasRole($user, $role)) {
            throw new DuplicateRoleAssignmentException();
        }
        $role = $this->getRoleReference($role);
        $user->addRole($role);
        $this->save($user);
    }

    public function userHasRole(AppUser $user, AppRole $role)
    {
        $role = $this->getRoleReference($role);
        return $user->hasRole($role);
    }

    /**
     * @param AppRole $role
     * @return AppRole
     */
    private function getRoleReference(AppRole $role)
    {
        /** @var AppRole $role */
        $role = $this->em->getReference(AppRole::class, $role->getId());

        return $role;
    }
}
