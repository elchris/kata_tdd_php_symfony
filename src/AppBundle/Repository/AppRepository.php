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
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    protected function save($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
        return $entity;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function qb()
    {
        return $this->em
            ->createQueryBuilder();
    }
}
