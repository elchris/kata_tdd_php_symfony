<?php


namespace AppBundle\Repository;

use AppBundle\Entity\Ride;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Ramsey\Uuid\Uuid;

class RideRepository extends AppRepository
{

    public function saveRide(Ride $ride)
    {
        $this->save($ride);
    }

    /**
     * @param Uuid $id
     * @return Ride
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getById(Uuid $id)
    {
        return $this->em->createQuery(
            'select r from E:Ride r where r.id = :id'
        )
        ->setParameter('id', $id)
        ->getSingleResult();
    }
}
