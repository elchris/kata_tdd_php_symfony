<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Ramsey\Uuid\Uuid;

class UserRepository extends AppRepository
{
    public function saveUser(AppUser $user)
    {
        $this->save($user);
    }

    /**
     * @param Uuid $id
     * @return AppUser
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getById(Uuid $id)
    {
        return $this->em->createQuery(
            'select u from E:AppUser u where u.id = :id'
        )
        ->setParameter('id', $id)
        ->getSingleResult();
    }

    /**
     * @param AppUser $user
     * @param AppRole $role
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function assignRoleToUser(AppUser $user, AppRole $role)
    {
        $user->assignRole($this->getRoleReference($role));
        $this->saveUser($user);
    }

    /**
     * @param AppRole $role
     * @return AppRole
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    private function getRoleReference(AppRole $role)
    {
        return $this->em->createQuery(
            'select r from E:AppRole r where r = :role'
        )
        ->setParameter('role', $role)
        ->getSingleResult();
    }
}
