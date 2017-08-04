<?php


namespace AppBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;

abstract class AppRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

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

    protected function query($dql) {
        return $this->em->createQuery($dql);
    }
}
