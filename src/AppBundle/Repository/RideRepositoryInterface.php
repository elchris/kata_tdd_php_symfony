<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEvent;
use AppBundle\Entity\RideEventType;

interface RideRepositoryInterface
{
    /**
     * @param AppUser $passenger
     * @param AppLocation $departure
     * @return Ride
     */
    public function createRide(AppUser $passenger, AppLocation $departure);

    /**
     * @param AppUser $passenger
     * @return Ride[]
     */
    public function getRidesForPassenger(AppUser $passenger);

    /**
     * @param RideEventType $type
     * @return RideEventType
     */
    public function getEventType(RideEventType $type);

    public function saveRideEvent(RideEvent $event);

    /**
     * @param Ride $ride
     * @return RideEvent
     */
    public function getLastEventForRide(Ride $ride);

    /**
     * @param Ride $ride
     * @param RideEventType $eventType
     * @return bool
     */
    public function isRideStatus(Ride $ride, RideEventType $eventType);

    /**
     * @param Ride $ride
     * @param AppUser $driver
     */
    public function assignDriverToRide(Ride $ride, AppUser $driver);

    public function assignDestinationToRide(Ride $ride, AppLocation $destination);
}