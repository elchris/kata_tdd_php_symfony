<?php


namespace AppBundle;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEvent;
use AppBundle\Entity\RideEventType;

class AppService
{
    /**
     * @var AppDao
     */
    private $dao;

    /**
     * @param AppDao $dao
     */
    public function __construct(AppDao $dao)
    {
        $this->dao = $dao;
    }

    public function newUser($firstName, $lastName)
    {
        $this->dao->newUser($firstName, $lastName);
    }

    /**
     * @param integer $userId
     * @return AppUser
     */
    public function getUserById($userId)
    {
        return $this->dao->getUserById($userId);
    }

    public function assignRoleToUser(AppUser $user, AppRole $role)
    {
        if (!$this->dao->isUserInRole($user, $role)) {
            $this->dao->assignRoleToUser($user, $role);
        } else {
            throw new RoleLifeCycleException(
                'User: '
                .$user->getFullName()
                .' is already of Role: '
                .$role->getName()
            );
        }
    }

    /**
     * @param AppUser $user
     * @return bool
     */
    public function isUserPassenger(AppUser $user)
    {
        return $this->dao->isUserInRole($user, AppRole::asPassenger());
    }

    public function isUserDriver(AppUser $user)
    {
        return $this->dao->isUserInRole($user, AppRole::asDriver());
    }

    /**
     * @param float $lat
     * @param float $long
     * @return AppLocation
     */
    public function getLocation($lat, $long)
    {
        return $this->dao->getOrCreateLocation(
            $lat,
            $long
        );
    }

    /**
     * @param AppUser $passenger
     * @param AppLocation $departure
     * @return Ride
     */
    public function createRide(AppUser $passenger, AppLocation $departure)
    {
        return $this->dao->createRide($passenger, $departure);
    }

    /**
     * @param AppUser $passenger
     * @return Ride[]
     */
    public function getRidesForPassenger(AppUser $passenger)
    {
        return $this->dao->getRidesForPassenger($passenger);
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

    public function prospectiveDriverMarkRideAs($ride, $type, $prospectiveDriver)
    {
        $this->markRideAsForActor($ride, $type, $prospectiveDriver);
    }

    /**
     * @param Ride $ride
     * @return RideEvent
     */
    public function getRideStatus(Ride $ride)
    {
        return $this->dao->getLastEventForRide($ride);
    }

    /**
     * @param Ride $ride
     * @param RideEventType $eventType
     * @return bool
     */
    public function isRide(Ride $ride, RideEventType $eventType)
    {
        return $this->dao->isRideStatus($ride, $eventType);
    }

    /**
     * @param Ride $ride
     * @param AppUser $driver
     */
    public function assignDriverToRide(Ride $ride, AppUser $driver)
    {
        $this->dao->assignDriverToRide($ride, $driver);
    }

    public function assignDestinationToRide(Ride $ride, AppLocation $destination)
    {
        $this->dao->assignDestinationToRide($ride, $destination);
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
            $this->dao->getEventType($type),
            $ride,
            $actor
        );
        $this->dao->saveRideEvent($event);
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
            !$this->isRide($ride, RideEventType::asAccepted())
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
            !
            (
                $this->isRide($ride, RideEventType::asRequested())
                ||
                $this->isRide($ride, RideEventType::asAccepted())
            )
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
     */
    private function validateRideLifecycle(Ride $ride, RideEventType $type)
    {
        $this->validateRequestedLifecycle($ride, $type);
        $this->validateAcceptedLifecycle($ride, $type);
        $this->validateInProgressLifeCycle($ride, $type);
        $this->validateCancelledLifeCycle($ride, $type);
        $this->validateCompletedLifeCycle($ride, $type);
        $this->validateRejectedLifecycle($ride, $type);
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
}
