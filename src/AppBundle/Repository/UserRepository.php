<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Exception\DuplicateRoleAssignmentException;
use Ramsey\Uuid\Uuid;

class UserRepository extends AppRepository
{
    /**
     * @param Uuid $userId
     * @return AppUser
     */
    public function getUserById(Uuid $userId)
    {
        return $this->em->createQuery(
            'select u from E:AppUser u where u.id = :userId'
        )
        ->setParameter('userId', $userId)
        ->getSingleResult();
    }

    public function assignRoleToUser(AppUser $user, AppRole $role)
    {
        if ($user->hasRole($role)) {
            throw new DuplicateRoleAssignmentException();
        }
        $role = $this->getRoleReference($role);
        $user->assignRole($role);
        $this->save($user);
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
