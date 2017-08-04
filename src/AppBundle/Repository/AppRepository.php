<?php


namespace AppBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;

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
     * @param $dql
     * @return Query
     */
    private function query($dql) {
        return $this->em->createQuery($dql);
    }

    /**
     * @param string $dql
     * @param array $params
     * @return mixed
     */
    protected function singleResultQuery($dql, $params)
    {
        $q = $this->query($dql);
        $this->setParameters($params, $q);

        return $q->getSingleResult();
    }

    protected function multipleResultsQuery($dql, $params)
    {
        $q = $this->query($dql);
        $this->setParameters($params, $q);
        return $q->getResult();
    }

    protected function firstSingleResultQuery($dql, $params)
    {
        $q = $this->query($dql);
        $this->setParameters($params, $q);
        $q->setMaxResults(1);
        return $q->getSingleResult();
    }

    /**
     * @param $params
     * @param $q
     */
    private function setParameters($params, Query $q)
    {
        foreach ($params as $key => $value) {
            $q->setParameter($key, $value);
        }
    }
}
