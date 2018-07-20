<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use Ramsey\Uuid\Uuid;

class UserRepository extends AppRepository
{

    public function saveUser(AppUser $user)
    {
        $this->save($user);
    }

    public function byId(Uuid $id) : AppUser
    {
        return $this->em->createQuery(
            'select u from E:AppUser u where u.id = :id'
        )
        ->setParameter('id', $id)
        ->getSingleResult();
    }

    public function getRole(AppRole $role) : AppRole
    {
        return $this->em->createQuery(
            'select r from E:AppRole r where r = :role'
        )
        ->setParameter('role', $role)
        ->getSingleResult();
    }
}
