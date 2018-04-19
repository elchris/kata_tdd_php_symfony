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
     * UserRepository constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save($object)
    {
        $this->em->persist($object);
        $this->em->flush();
        //$this->em->clear();
    }
}
