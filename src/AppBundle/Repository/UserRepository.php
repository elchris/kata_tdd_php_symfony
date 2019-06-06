<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Ramsey\Uuid\Uuid;

class UserRepository extends AppRepository
{

    public function saveAndGet(AppUser $user) : AppUser
    {
        $this->save($user);
        return $user;
    }

    /**
     * @param Uuid $id
     * @return AppUser
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function byId(Uuid $id) : AppUser
    {
        return $this->em->createQuery(
            'select u from E:AppUser u where u.id = :id'
        )
        ->setParameter('id', $id)
        ->getSingleResult();
    }

    /**
     * @param AppRole $roleToFind
     * @return AppRole
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getRoleReference(AppRole $roleToFind) : AppRole
    {
        return $this->em->createQuery(
            'select r from E:AppRole r where r = :role'
        )
        ->setParameter('role', $roleToFind)
        ->getSingleResult();
    }
}
