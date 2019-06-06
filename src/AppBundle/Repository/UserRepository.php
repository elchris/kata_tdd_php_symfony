<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AppUser;
use Ramsey\Uuid\Uuid;

class UserRepository extends AppRepository
{

    public function saveAndGet(AppUser $user) : AppUser
    {
        $this->save($user);
        return $user;
    }

    public function byId(Uuid $id) : AppUser
    {
        return $this->em->createQuery(
            'select u from E:AppUser u where u.id = :id'
        )
        ->setParameter('id', $id)
        ->getSingleResult();
    }
}
