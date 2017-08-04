<?php


namespace AppBundle\Repository;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEvent;
use AppBundle\Entity\RideEventType;
use Doctrine\ORM\NoResultException;

class RideRepository extends AppRepository implements RideRepositoryInterface
{
    /**
     * @param AppUser $passenger
     * @param AppLocation $departure
     * @return Ride
     */
    public function createRide(AppUser $passenger, AppLocation $departure)
    {
        $savedRide = $this->save(new Ride(
            $passenger,
            $departure
        ));
        return $savedRide;
    }

    /**
     * @param AppUser $passenger
     * @return Ride[]
     */
    public function getRidesForPassenger(AppUser $passenger)
    {
        return $this->query(
            'select r from E:Ride r where r.passenger = :passenger'
        )
            ->setParameter('passenger', $passenger)
            ->getResult();
    }

    /**
     * @param RideEventType $type
     * @return RideEventType
     */
    public function getEventType(RideEventType $type)
    {
        return $this->query(
            'select t from E:RideEventType t where t = :type'
        )
            ->setParameter('type', $type)
            ->getSingleResult();
    }

    public function saveRideEvent(RideEvent $event)
    {
        $this->save($event);
    }

    /**
     * @param Ride $ride
     * @return RideEvent
     */
    public function getLastEventForRide(Ride $ride)
    {
        return $this->qb()
            ->select('e')
            ->from(RideEvent::class, 'e')
            ->where('e.ride = :ride')
            ->orderBy('e.created', 'desc')
            ->orderBy('e.id', 'desc')
            ->getQuery()
            ->setMaxResults(1)
            ->setParameter('ride', $ride)
            ->getSingleResult()
            ;
    }

    /**
     * @param Ride $ride
     * @param RideEventType $eventType
     * @return bool
     */
    public function isRideStatus(Ride $ride, RideEventType $eventType)
    {
        try {
            $lastEvent = $this->getLastEventForRide($ride);
            return $lastEvent->is($eventType);
        } catch (NoResultException $e) {
            return false;
        }
    }

    /**
     * @param Ride $ride
     * @param AppUser $driver
     */
    public function assignDriverToRide(Ride $ride, AppUser $driver)
    {
        $ride->assignDriver($driver);
        $this->save($ride);
    }

    public function assignDestinationToRide(Ride $ride, AppLocation $destination)
    {
        $ride->assignDestination($destination);
        $this->save($ride);
    }
}
