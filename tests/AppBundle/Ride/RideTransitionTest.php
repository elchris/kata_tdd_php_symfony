<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEventType;
use AppBundle\Exception\ActingDriverIsNotAssignedDriverException;
use AppBundle\Exception\DuplicateRoleAssignmentException;
use AppBundle\Exception\RideLifeCycleException;
use AppBundle\Exception\RideNotFoundException;
use AppBundle\Exception\UnauthorizedOperationException;
use AppBundle\Exception\UserNotFoundException;
use AppBundle\Exception\UserNotInDriverRoleException;
use AppBundle\Exception\UserNotInPassengerRoleException;

class RideTransitionTest extends AppTestCase
{
    /**
     * @throws ActingDriverIsNotAssignedDriverException
     * @throws DuplicateRoleAssignmentException
     * @throws RideLifeCycleException
     * @throws RideNotFoundException
     * @throws UserNotInDriverRoleException
     * @throws UserNotInPassengerRoleException
     * @throws UserNotFoundException
     * @throws UnauthorizedOperationException
     */
    public function testPatchRideLifeCycle()
    {
        $driver = $this->user()->getNewDriver();
        $ride = $this->ride()->getSavedNewRideWithPassengerAndDestination();

        $this->assertRidePatchEvent($ride, RideEventType::ACCEPTED_ID, $driver);
        $this->assertRidePatchEvent($ride, RideEventType::IN_PROGRESS_ID, $driver);
        $this->assertRidePatchEvent($ride, RideEventType::COMPLETED_ID, $driver);
        $this->assertRidePatchEvent($ride, null, $driver);
    }

    /**
     * @throws ActingDriverIsNotAssignedDriverException
     * @throws DuplicateRoleAssignmentException
     * @throws RideLifeCycleException
     * @throws RideNotFoundException
     * @throws UserNotFoundException
     * @throws UserNotInDriverRoleException
     * @throws UserNotInPassengerRoleException
     * @throws UnauthorizedOperationException
     */
    public function testPatchRideLifeCycleNullDriverIdAndEventId()
    {
        $ride = $this->ride()->getSavedNewRideWithPassengerAndDestination();
        $patchedRide = $this->ride()->updateRideByEventId(
            $ride,
            null,
            null
        );
        self::assertTrue(
            RideEventType::requested()->equals(
                $this->ride()->getRideStatus($patchedRide)
            )
        );
    }

    /**
     * @param Ride $ride
     * @param string $eventId |null
     * @param AppUser $driver
     * @return Ride
     * @throws ActingDriverIsNotAssignedDriverException
     * @throws RideLifeCycleException
     * @throws RideNotFoundException
     * @throws UserNotInDriverRoleException
     * @throws UserNotFoundException
     * @throws UnauthorizedOperationException
     */
    private function assertRidePatchEvent(Ride $ride, string $eventId = null, AppUser $driver): Ride
    {
        $patchedRide = $this->ride()->updateRideByEventId(
            $ride,
            $eventId,
            $driver->getId()->toString()
        );

        self::assertTrue($ride->isDrivenBy($driver));
        if (! is_null($eventId)) {
            self::assertTrue(
                RideEventType::newById(intval($eventId))->equals(
                    $this->ride()->getRideStatus($patchedRide)
                )
            );
        } else {
            self::assertFalse(
                RideEventType::newById(intval($eventId))->equals(
                    $this->ride()->getRideStatus($patchedRide)
                )
            );
        }
        return $patchedRide;
    }
}
