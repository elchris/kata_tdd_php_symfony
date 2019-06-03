<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Ride;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Ramsey\Uuid\Uuid;

class RideRepository extends AppRepository
{
    public function saveRide(Ride $ride) : Ride
    {
        $this->save($ride);
        return $ride;
    }

    /**
     * @param Uuid $rideId
     * @return Ride
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function byId(Uuid $rideId) : Ride
    {
        return $this->em->createQuery(
            'select r from E:Ride r where r.id = :id'
        )
        ->setParameter('id', $rideId)
        ->getSingleResult();
    }
}
