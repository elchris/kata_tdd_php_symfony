<?php


namespace AppBundle\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class AppRepository extends EntityRepository
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @param EntityManagerInterface $em
     * @param string $entityName
     */
    public function __construct(EntityManagerInterface $em, $entityName)
    {
        /** @var EntityManager $em **/
        parent::__construct($em, $em->getClassMetadata($entityName));
        $this->em = $em;
    }

    public function save($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
    }
}
