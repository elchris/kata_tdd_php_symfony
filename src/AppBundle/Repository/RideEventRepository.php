<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEvent;

class RideEventRepository extends AppRepository
{
    /**
     * @param Ride $ride
     * @return RideEvent
     */
    public function getLastEventForRide(Ride $ride)
    {
        return $this->em->createQuery(
            'select e from E:RideEvent e where e.ride = :ride order by e.created desc, e.id desc'
        )
        ->setMaxResults(1)
        ->setParameter('ride', $ride)
        ->getSingleResult()
        ;
    }
}
