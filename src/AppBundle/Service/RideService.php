<?php

namespace AppBundle\Service;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEventType;
use AppBundle\Exception\UserNotPassengerException;
use AppBundle\Repository\RideEventRepository;
use AppBundle\Repository\RideRepository;

class RideService
{
    /**
     * @var RideRepository
     */
    private $rideRepository;
    /**
     * @var RideEventRepository
     */
    private $rideEventRepository;

    /**
     * RideService constructor.
     * @param RideRepository $rideRepository
     * @param RideEventRepository $rideEventRepository
     */
    public function __construct(RideRepository $rideRepository, RideEventRepository $rideEventRepository)
    {
        $this->rideRepository = $rideRepository;
        $this->rideEventRepository = $rideEventRepository;
    }

    public function newRide(AppUser $passenger, AppLocation $departure)
    {
        if (!$passenger->hasRole(AppRole::passenger())) {
            throw new UserNotPassengerException();
        }

        $newRide = new Ride($passenger, $departure);
        $this->rideRepository->save($newRide);

        $this->rideEventRepository->markRideStatusByActor(
            $newRide,
            $newRide->getPassenger(),
            RideEventType::requested()
        );

        return $newRide;
    }

    public function getRideStatus(Ride $ride)
    {
        $lastEvent = $this->rideEventRepository->getLastEventForRide($ride);
        return $lastEvent->getStatus();
    }
}
