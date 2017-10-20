<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEvent;
use AppBundle\Entity\RideEventType;

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

    public function markRideStatusByActor(
        Ride $ride,
        AppUser $actor,
        RideEventType $status
    ) {
        /** @var RideEventType $status */
        $status = $this->em->getReference(
            RideEventType::class,
            $status->getId()
        );

        $newEvent = new RideEvent(
            $ride,
            $actor,
            $status
        );

        $this->save($newEvent);

        return $newEvent;
    }
}
