<?php

namespace AppBundle\Service;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEventType;
use AppBundle\Exception\RideLifeCycleException;
use AppBundle\Exception\UserNotDriverException;
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

    public function acceptRide(Ride $ride, AppUser $driver)
    {
        $this->validateUserIsDriver($driver);
        $this->validateRideIsRequested($ride);

        $this->rideEventRepository->markRideStatusByActor(
            $ride,
            $driver,
            RideEventType::accepted()
        );

        $this->rideRepository->assignDriverToRide(
            $ride,
            $driver
        );

        return $ride;
    }

    public function markRideInProgress(Ride $acceptedRide, AppUser $driver)
    {
        $this->validateRideIsAccepted($acceptedRide);
        $this->validateUserIsDriver($driver);

        $this->rideEventRepository->markRideStatusByActor(
            $acceptedRide,
            $driver,
            RideEventType::inProgress()
        );
        return $acceptedRide;
    }

    /**
     * @param AppUser $driver
     * @throws UserNotDriverException
     */
    protected function validateUserIsDriver(AppUser $driver)
    {
        if (!$driver->hasRole(AppRole::driver())) {
            throw new UserNotDriverException();
        }
    }

    /**
     * @param Ride $ride
     * @throws RideLifeCycleException
     */
    protected function validateRideIsRequested(Ride $ride)
    {
        if (!RideEventType::requested()->equals(
            $this->getRideStatus($ride)
        )) {
            throw new RideLifeCycleException();
        }
    }

    /**
     * @param Ride $acceptedRide
     * @throws RideLifeCycleException
     */
    protected function validateRideIsAccepted(Ride $acceptedRide)
    {
        if (!RideEventType::accepted()->equals(
            $this->getRideStatus($acceptedRide)
        )) {
            throw new RideLifeCycleException();
        }
    }
}
