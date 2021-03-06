<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEvent;
use AppBundle\Entity\RideEventType;
use AppBundle\Exception\RideNotFoundException;

class RideEventRepository extends AppRepository implements RideEventRepositoryInterface
{
    /**
     * @param Ride $ride
     * @return RideEvent
     * @throws RideNotFoundException
     */
    public function getLastEventForRide(Ride $ride): RideEvent
    {
        try {
            return $this->em->createQuery(
                'select e from E:RideEvent e where e.ride = :ride order by e.created desc, e.id desc'
            )
                ->setMaxResults(1)
                ->setParameter('ride', $ride)
                ->getSingleResult();
        } catch (\Exception $e) {
            throw new RideNotFoundException();
        }
    }

    public function markRideStatusByActor(
        Ride $ride,
        AppUser $actor,
        RideEventType $status
    ): RideEvent {
        $newEvent = new RideEvent(
            $ride,
            $actor,
            $this->getStatusReference($status)
        );

        $this->save($newEvent);

        return $newEvent;
    }

    public function markRideStatusByPassenger(Ride $ride, RideEventType $status): RideEvent
    {
        $passengerEvent = $ride->getPassengerTransaction($this->getStatusReference($status));
        $this->save($passengerEvent);
        return $passengerEvent;
    }

    /**
     * @param RideEventType $status
     * @return RideEventType
     */
    private function getStatusReference(RideEventType $status) : RideEventType
    {
        /** @var RideEventType $status */
        $status = $this->em->getRepository(
            RideEventType::class
        )->find($status->getId());

        return $status;
    }
}
