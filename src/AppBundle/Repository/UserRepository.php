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

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(AppUser $newUser)
    {
        $this->em->persist($newUser);
        $this->em->flush();
    }
}
