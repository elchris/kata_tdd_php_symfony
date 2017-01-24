<?php


namespace AppBundle\Repository;

use AppBundle\Entity\RideEventType;
use Doctrine\ORM\EntityManagerInterface;

class AppRepository
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    protected function save($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    protected function getRequestedEventType()
    {
        return $this->getRideEventType(RideEventType::asRequested());
    }

    protected function getAcceptedEventType()
    {
        return $this->getRideEventType(RideEventType::asAcceptedByDriver());
    }

    /**
     * @param RideEventType $type
     * @return RideEventType
     */
    protected function getRideEventType(RideEventType $type)
    {
        return $this
            ->em
            ->createQuery(
                'select t from E:RideEventType t where t = :type'
            )
            ->setParameter('type', $type)
            ->getSingleResult();
    }
}
