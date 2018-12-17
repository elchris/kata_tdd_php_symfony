<?php


namespace AppBundle\Repository;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use Ramsey\Uuid\Uuid;

class UserRepository extends AppRepository
{
    public function saveUser(AppUser $newUser)
    {
        $this->save($newUser);
    }

    public function byId(Uuid $userId)
    {
        return $this->em->createQuery(
            'select u from E:AppUser u where u.id = :userId'
        )
        ->setParameter('userId', $userId)
        ->getSingleResult()
        ;
    }

    public function getRoleReference(AppRole $roleLookup)
    {
        return $this->em->createQuery(
            'select r from E:AppRole r where r = :role'
        )
        ->setParameter('role', $roleLookup)
        ->getSingleResult();
    }
}
