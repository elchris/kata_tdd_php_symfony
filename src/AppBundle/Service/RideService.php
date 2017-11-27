<?php

namespace AppBundle\Service;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEventType;
use AppBundle\Exception\ActingDriverIsNotAssignedDriverException;
use AppBundle\Exception\RideLifeCycleException;
use AppBundle\Exception\UserNotInDriverRoleException;
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

        $this->rideEventRepository->markRideStatusByPassenger(
            $newRide,
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
        $this->validateUserHasDriverRole($driver);
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
        $this->validateUserHasDriverRole($driver);
        $this->validateAttemptingDriverIsAssignedDriver($acceptedRide, $driver);

        $this->rideEventRepository->markRideStatusByActor(
            $acceptedRide,
            $driver,
            RideEventType::inProgress()
        );
        return $acceptedRide;
    }

    public function markRideCompleted(Ride $rideInProgress, AppUser $driver)
    {
        $this->validateAttemptingDriverIsAssignedDriver($rideInProgress, $driver);
        $this->validateRideIsInProgress($rideInProgress);

        $this->rideEventRepository->markRideStatusByActor(
            $rideInProgress,
            $driver,
            RideEventType::completed()
        );
        return $rideInProgress;
    }

    /**
     * @param AppUser $driver
     * @throws UserNotInDriverRoleException
     */
    protected function validateUserHasDriverRole(AppUser $driver)
    {
        if (!$driver->hasRole(AppRole::driver())) {
            throw new UserNotInDriverRoleException();
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

    /**
     * @param Ride $acceptedRide
     * @param AppUser $driver
     * @throws ActingDriverIsNotAssignedDriverException
     */
    protected function validateAttemptingDriverIsAssignedDriver(Ride $acceptedRide, AppUser $driver)
    {
        if (!$acceptedRide->isDrivenBy($driver)) {
            throw new ActingDriverIsNotAssignedDriverException();
        }
    }

    /**
     * @param Ride $rideInProgress
     * @throws RideLifeCycleException
     */
    protected function validateRideIsInProgress(Ride $rideInProgress)
    {
        if (!RideEventType::inProgress()->equals($this->getRideStatus($rideInProgress))) {
            throw new RideLifeCycleException();
        }
    }
}
