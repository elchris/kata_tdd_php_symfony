<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use Ramsey\Uuid\Uuid;

class UserRepository extends AppRepository
{
    public function saveUser(AppUser $userToSave)
    {
        $this->save($userToSave);
    }

    /**
     * @param Uuid $userId
     * @return AppUser
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
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
        $roleReference = $this->getRoleReference($role);
        $user->assignRole($roleReference);
        $this->saveUser($user);
    }

    private function getRoleReference(AppRole $role)
    {
        return $this->em->createQuery(
            'select r from E:AppRole r where r = :role'
        )
        ->setParameter('role', $role)
        ->getSingleResult();
    }
}
