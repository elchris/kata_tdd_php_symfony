<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AppUser;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Ramsey\Uuid\Uuid;

class UserRepository extends AppRepository
{

    /**
     * UserRepository constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em);
    }

    public function saveUser(AppUser $newUser)
    {
        $this->save($newUser);
    }

    /**
     * @param Uuid $userId
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function byId(Uuid $userId) : AppUser
    {
        return $this->em->createQuery(
            'select u from E:AppUser u where u.id = :id'
        )
        ->setParameter('id', $userId)
        ->getSingleResult();
    }
}
