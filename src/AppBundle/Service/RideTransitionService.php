<?php


namespace AppBundle\Service;

use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEventType;
use AppBundle\Exception\ActingDriverIsNotAssignedDriverException;
use AppBundle\Exception\RideLifeCycleException;
use AppBundle\Exception\RideNotFoundException;
use AppBundle\Exception\UserNotFoundException;
use AppBundle\Exception\UserNotInDriverRoleException;
use Ramsey\Uuid\Uuid;

class RideTransitionService
{
    /**
     * @var RideService
     */
    private $rideService;
    /**
     * @var UserService
     */
    private $userService;

    /**
     * @param RideService $rideService
     * @param UserService $userService
     */
    public function __construct(RideService $rideService, UserService $userService)
    {
        $this->rideService = $rideService;
        $this->userService = $userService;
    }

    /**
     * @param Ride $ride
     * @param string $eventId|null
     * @param string $driverId|null
     * @return Ride
     * @throws ActingDriverIsNotAssignedDriverException
     * @throws RideLifeCycleException
     * @throws RideNotFoundException
     * @throws UserNotFoundException
     * @throws UserNotInDriverRoleException
     */
    public function updateRideByEventId(Ride $ride, string $eventId = null, string $driverId = null)
    {
        if (! is_null($driverId)) {
            /** @var Uuid $uuid */
            $uuid = Uuid::fromString($driverId);
            $driver = $this->userService->getUserById($uuid);
            $eventToProcess = RideEventType::newById(intval($eventId));
            $this->patchRideAcceptance($eventToProcess, $ride, $driver);
            $this->patchRideInProgress($eventToProcess, $ride, $driver);
            $this->patchRideCompleted($eventToProcess, $ride, $driver);
        }
        return $ride;
    }

    /**
     * @param RideEventType $eventToProcess
     * @param Ride $rideToPatch
     * @param AppUser $driver
     * @throws RideLifeCycleException
     * @throws RideNotFoundException
     * @throws UserNotInDriverRoleException
     */
    private function patchRideAcceptance(RideEventType $eventToProcess, Ride $rideToPatch, AppUser $driver): void
    {
        if (RideEventType::accepted()->equals($eventToProcess)) {
            $this->rideService->acceptRide($rideToPatch, $driver);
        }
    }

    /**
     * @param RideEventType $eventToProcess
     * @param Ride $rideToPatch
     * @param AppUser $driver
     * @throws ActingDriverIsNotAssignedDriverException
     * @throws RideLifeCycleException
     * @throws RideNotFoundException
     * @throws UserNotInDriverRoleException
     */
    private function patchRideInProgress(RideEventType $eventToProcess, Ride $rideToPatch, AppUser $driver)
    {
        if (RideEventType::inProgress()->equals($eventToProcess)) {
            $this->rideService->markRideInProgress($rideToPatch, $driver);
        }
    }

    /**
     * @param RideEventType $eventToProcess
     * @param Ride $rideToPatch
     * @param AppUser $driver
     * @throws ActingDriverIsNotAssignedDriverException
     * @throws RideLifeCycleException
     * @throws RideNotFoundException
     */
    private function patchRideCompleted(RideEventType $eventToProcess, Ride $rideToPatch, AppUser $driver)
    {
        if (RideEventType::completed()->equals($eventToProcess)) {
            $this->rideService->markRideCompleted($rideToPatch, $driver);
        }
    }
}
