<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;

abstract class AppRepository
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * DoctrineUserRepository constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save($object): void
    {
        $this->em->persist($object);
        $this->em->flush();
        //$this->em->clear();
    }
}
