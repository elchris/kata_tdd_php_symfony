<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AppUser;
use Doctrine\ORM\EntityManagerInterface;

class UserRepository
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function save(AppUser $user)
    {
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @param integer $id
     * @return AppUser
     */
    public function getUserById($id)
    {
        return $this->em->createQuery(
        'select u from E:AppUser u where u.id = :id'
        )
        ->setParameter('id', $id)
        ->getSingleResult();
    }
}
