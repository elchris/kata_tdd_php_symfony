<?php


namespace AppBundle\Repository;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEvent;

class RideRepository extends AppRepository
{
    public function createRideForUser(
        AppUser $savedUserOne,
        AppLocation $departure,
        AppLocation $destination
    )
    {
        $departure = $this->getOrAddLocation($departure);
        $destination = $this->getOrAddLocation($destination);

        $ride = new Ride(
            $savedUserOne,
            $departure,
            $destination
        );
        $this->save($ride);
    }

    /**
     * @param AppLocation $location
     * @return AppLocation
     */
    private function getOrAddLocation(AppLocation $location)
    {
        $existingLocations = $this->getExistingLocations($location);
        if (sizeof($existingLocations) === 1) {
            return $existingLocations[0];
        } else {
            $this->save($location);
            return $this->getExistingLocations($location)[0];
        }
    }

    private function getExistingLocations(AppLocation $location)
    {
        return $this->em->createQuery(
//TODO: look out for floating-point
//            '
//                select l
//                    from E:AppLocation l
//                    where
//                    ((abs(l.lat - :lat)) <= 0.00001)
//                    and
//                    ((abs(l.long - :long)) <= 0.00001)
//            '
            '
                select l
                    from E:AppLocation l
                    where
                    l.lat = :lat
                    and
                    l.long = :long
            '
        )
            ->setParameter('lat', $location->getLat())
            ->setParameter('long', $location->getLong())
            ->getResult();
    }
    public function markRideAsRequested(Ride $ride, AppUser $driver)
    {
        $requestedType = $this->getRequestedEventType();
        $rideEvent = new RideEvent(
            $ride,
            $requestedType,
            $driver
        );
        $this->save($rideEvent);
    }

    /**
     * @param Ride $ride
     * @return RideEvent
     */
    public function getLastEventForRide(Ride $ride)
    {
        return $this->em->createQuery(
            '
            select event
            from E:RideEvent event
            where event.ride = :ride
            order by
              event.created desc,
              event.id desc
            '
        )
            ->setParameter('ride', $ride)
            ->setMaxResults(1)
            ->getSingleResult();
    }

    /**
     * @param Ride $ride
     * @param AppUser $driver
     */
    public function makeEventRideAsAcceptedByDriver(Ride $ride, AppUser $driver)
    {
        $acceptedType = $this->getAcceptedEventType();
        $rideEvent = new RideEvent(
            $ride,
            $acceptedType,
            $driver
        );
        $this->save($rideEvent);
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
}
