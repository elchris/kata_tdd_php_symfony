<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AppUser;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Ramsey\Uuid\Uuid;

class UserRepository extends AppRepository
{

    public function saveUser(AppUser $newUser) : AppUser
    {
        $this->save($newUser);
        return $newUser;
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
}
