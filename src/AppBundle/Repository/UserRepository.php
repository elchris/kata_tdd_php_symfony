<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AppUser;
use Ramsey\Uuid\Uuid;
use AppBundle\Entity\AppRole;

class UserRepository extends AppRepository
{
    public function saveUser(AppUser $user)
    {
        $this->save($user);
    }

    /**
     * @param Uuid $id
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getUserById(Uuid $id)
    {
        return $this->em->createQuery(
            'select u from E:AppUser u where u.id = :userId'
        )
        ->setParameter('userId', $id)
        ->getSingleResult();
    }

    public function assignRoleToUser(AppUser $user, AppRole $role)
    {
        $role = $this->getRoleReference($role);
        $user->assignRole($role);
        $this->saveUser($user);
    }

    public function getRoleReference(AppRole $role)
    {
        return $this->em->createQuery(
            'select r from E:AppRole r where r = :role'
        )
        ->setParameter('role', $role)
        ->getSingleResult();
    }
}
