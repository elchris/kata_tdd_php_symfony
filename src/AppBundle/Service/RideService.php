<?php


namespace AppBundle\Service;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEvent;
use AppBundle\Entity\RideEventType;
use AppBundle\Repository\RideRepository;
use AppBundle\RideEventLifeCycleException;

class RideService
{
    /**
     * @var RideRepository
     */
    private $rideRepository;

    /**
     * @param RideRepository $rideRepository
     */
    public function __construct($rideRepository)
    {
        $this->rideRepository = $rideRepository;
    }

    /**
     * @param AppUser $passenger
     * @param AppLocation $departure
     * @return Ride
     */
    public function createRide(AppUser $passenger, AppLocation $departure)
    {
        return $this->rideRepository->createRide($passenger, $departure);
    }

    /**
     * @param AppUser $passenger
     * @return Ride[]
     */
    public function getRidesForPassenger(AppUser $passenger)
    {
        return $this->rideRepository->getRidesForPassenger($passenger);
    }

    public function passengerMarkRideAs(Ride $ride, RideEventType $type)
    {
        $actor = $ride->getPassenger();
        $this->markRideAsForActor($ride, $type, $actor);
    }

    public function driverMarkRideAs(Ride $ride, RideEventType $type)
    {
        $actor = $ride->getDriver();
        $this->markRideAsForActor($ride, $type, $actor);
    }

    public function prospectiveDriverMarkRideAs(Ride $ride, RideEventType $type, AppUser $prospectiveDriver)
    {
        $this->markRideAsForActor($ride, $type, $prospectiveDriver);
    }

    /**
     * @param Ride $ride
     * @return RideEvent
     */
    public function getRideStatus(Ride $ride)
    {
        return $this->rideRepository->getLastEventForRide($ride);
    }

    /**
     * @param Ride $ride
     * @param RideEventType $eventType
     * @return bool
     */
    public function isRide(Ride $ride, RideEventType $eventType)
    {
        return $this->rideRepository->isRideStatus($ride, $eventType);
    }

    /**
     * @param Ride $ride
     * @param AppUser $driver
     */
    public function assignDriverToRide(Ride $ride, AppUser $driver)
    {
        $this->rideRepository->assignDriverToRide($ride, $driver);
    }

    public function assignDestinationToRide(Ride $ride, AppLocation $destination)
    {
        $this->rideRepository->assignDestinationToRide($ride, $destination);
    }

    public function requestRide(AppUser $passenger, AppLocation $departure)
    {
        $newRide = $this->createRide($passenger, $departure);
        $this->passengerMarkRideAs($newRide, RideEventType::asRequested());
    }

    public function driverAcceptRide(Ride $ride, AppUser $driver)
    {
        $this->prospectiveDriverMarkRideAs($ride, RideEventType::asAccepted(), $driver);
        $this->assignDriverToRide($ride, $driver);
    }

    public function setDestinationForRide(Ride $ride, AppLocation $destination)
    {
        $this->passengerMarkRideAs($ride, RideEventType::asDestination());
        $this->assignDestinationToRide($ride, $destination);
    }

    public function startRide(Ride $ride)
    {
        $this->driverMarkRideAs($ride, RideEventType::inProgress());
    }

    public function completeRide(Ride $ride)
    {
        $this->driverMarkRideAs($ride, RideEventType::asCompleted());
    }

    /**
     * @param Ride $ride
     * @param RideEventType $type
     * @param AppUser $actor
     * @throws RideEventLifeCycleException
     */
    private function markRideAsForActor(Ride $ride, RideEventType $type, AppUser $actor)
    {
        $this->validateRideLifecycle($ride, $type);
        $event = new RideEvent(
            $this->rideRepository->getEventType($type),
            $ride,
            $actor
        );
        $this->rideRepository->saveRideEvent($event);
    }

    /**
     * @param Ride $ride
     * @param RideEventType $type
     * @throws RideEventLifeCycleException
     */
    private function validateRequestedLifecycle(Ride $ride, RideEventType $type)
    {
        if (
            $type->equals(RideEventType::asRequested())
            &&
            $this->isRide($ride, RideEventType::asRequested())
        ) {
            throw new RideEventLifeCycleException('Ride is already requested.');
        }
    }

    /**
     * @param Ride $ride
     * @param RideEventType $type
     * @throws RideEventLifeCycleException
     */
    private function validateAcceptedLifecycle(Ride $ride, RideEventType $type)
    {
        if (
            $type->equals(RideEventType::asAccepted())
            &&
            !$this->isRide($ride, RideEventType::asRequested())
        ) {
            throw new RideEventLifeCycleException();
        }
    }

    /**
     * @param Ride $ride
     * @param RideEventType $type
     * @throws RideEventLifeCycleException
     */
    private function validateInProgressLifeCycle(Ride $ride, RideEventType $type)
    {
        if (
            $type->equals(RideEventType::inProgress())
            &&
            ! $this->isRide($ride, RideEventType::asDestination())
        ) {
            throw new RideEventLifeCycleException();
        }
    }

    /**
     * @param Ride $ride
     * @param RideEventType $type
     * @throws RideEventLifeCycleException
     */
    private function validateCancelledLifeCycle(Ride $ride, RideEventType $type)
    {
        if (
            $type->equals(RideEventType::asCancelled())
            &&
            $this->rideIsAlreadyCommitted($ride)
        ) {
            throw new RideEventLifeCycleException();
        }
    }

    /**
     * @param Ride $ride
     * @param RideEventType $type
     * @throws RideEventLifeCycleException
     */
    private function validateCompletedLifeCycle(Ride $ride, RideEventType $type)
    {
        if (
            $type->equals(RideEventType::asCompleted())
            &&
            (!$this->isRide($ride, RideEventType::inProgress()))
        ) {
            throw new RideEventLifeCycleException();
        }
    }

    /**
     * @param Ride $ride
     * @param RideEventType $type
     * @throws RideEventLifeCycleException
     */
    private function validateRejectedLifecycle(Ride $ride, RideEventType $type)
    {
        if (
            $type->equals(RideEventType::asRejected())
            &&
            (!$this->isRide($ride, RideEventType::asRequested()))
        ) {
            throw new RideEventLifeCycleException();
        }
    }

    /**
     * @param Ride $ride
     * @param RideEventType $type
     * @throws RideEventLifeCycleException
     */
    private function validateDestinationLifeCycle(Ride $ride, RideEventType $type)
    {
        if (
            $type->equals(RideEventType::asDestination())
            &&
            $this->rideIsAlreadyCommitted($ride)
        ) {
            throw new RideEventLifeCycleException();
        }
    }

    /**
     * @param Ride $ride
     * @param RideEventType $type
     * @throws RideEventLifeCycleException
     */
    private function validateRideLifecycle(Ride $ride, RideEventType $type)
    {
        $this->validateRequestedLifecycle($ride, $type);
        $this->validateAcceptedLifecycle($ride, $type);
        $this->validateDestinationLifeCycle($ride, $type);
        $this->validateInProgressLifeCycle($ride, $type);
        $this->validateCancelledLifeCycle($ride, $type);
        $this->validateCompletedLifeCycle($ride, $type);
        $this->validateRejectedLifecycle($ride, $type);
    }

    /**
     * @param Ride $ride
     * @return bool
     */
    private function rideIsAlreadyCommitted(Ride $ride)
    {
        return !
        (
            $this->isRide($ride, RideEventType::asRequested())
            ||
            $this->isRide($ride, RideEventType::asAccepted())
        );
    }
}
