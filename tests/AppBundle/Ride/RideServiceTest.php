<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEventType;
use AppBundle\Exception\ActingDriverIsNotAssignedDriverException;
use AppBundle\Exception\DuplicateRoleAssignmentException;
use AppBundle\Exception\RideLifeCycleException;
use AppBundle\Exception\RideNotFoundException;
use AppBundle\Exception\UserNotFoundException;
use AppBundle\Exception\UserNotInDriverRoleException;
use AppBundle\Exception\UserNotInPassengerRoleException;

class RideServiceTest extends AppTestCase
{
    /**
     * @throws UserNotInPassengerRoleException
     * @throws DuplicateRoleAssignmentException
     */
    public function testCreateRide()
    {
        $newRide = $this->ride()->getSavedNewRideWithPassengerAndDestination();

        self::assertInstanceOf(Ride::class, $newRide);
        self::assertNotEmpty($newRide->getId());
    }

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotInPassengerRoleException
     * @throws RideNotFoundException
     */
    public function testGetRideById()
    {
        $newRide = $this->ride()->getSavedNewRideWithPassengerAndDestination();

        $retrievedRide = $this->ride()->getRideById($newRide->getId());

        self::assertTrue($newRide->is($retrievedRide));
    }

    /**
     * 1) the user of the Ride must be a passenger
     * 2) the Ride must not have already been requested
     * @throws UserNotInPassengerRoleException
     */

    public function testRideUserNotPassengerThrowsRoleException()
    {
        $notPassengerUser = $this->user()->getSavedUser();
        self::assertFalse($this->user()->isPassenger($notPassengerUser));
        $departure = $this->location()->getSavedHomeLocation();

        $this->expectException(UserNotInPassengerRoleException::class);

        $this->ride()->getNewRide($notPassengerUser, $departure);
    }

    /**
     * @throws UserNotInPassengerRoleException
     * @throws DuplicateRoleAssignmentException
     * @throws RideNotFoundException
     */
    public function testGetRideStatusIsRequestedWhenNew()
    {
        $newRide = $this->ride()->getSavedNewRideWithPassengerAndDestination();
        $rideStatus = $this->ride()->getRideStatus($newRide);

        self::assertTrue(RideEventType::requested()->equals($rideStatus));
    }

    /**
     * @throws RideLifeCycleException
     * @throws UserNotInDriverRoleException
     * @throws UserNotInPassengerRoleException
     * @throws DuplicateRoleAssignmentException
     * @throws RideNotFoundException
     */
    public function testAcceptRideByProspectiveDriver()
    {
        $newRide = $this->ride()->getSavedNewRideWithPassengerAndDestination();
        $newDriver = $this->user()->getNewDriver();

        $acceptedRide = $this->ride()->acceptRide($newRide, $newDriver);
        $rideStatus = $this->ride()->getRideStatus($newRide);

        self::assertTrue(RideEventType::accepted()->equals($rideStatus));
        self::assertTrue($acceptedRide->isDrivenBy($newDriver));
    }

    /**
     * @throws RideLifeCycleException
     * @throws UserNotInDriverRoleException
     * @throws UserNotInPassengerRoleException
     * @throws DuplicateRoleAssignmentException
     * @throws RideNotFoundException
     */
    public function testAcceptingNonRequestedRideThrowsException()
    {
        $newRide = $this->ride()->getSavedNewRideWithPassengerAndDestination();
        $winningDriver = $this->user()->getNewDriverWithName('Winning', 'Driver');
        $losingDriver = $this->user()->getNewDriverWithName('Losing', 'Driver');
        $this->ride()->acceptRide($newRide, $winningDriver);

        $this->expectException(RideLifeCycleException::class);
        $this->ride()->acceptRide($newRide, $losingDriver);
    }

    /**
     * @throws RideLifeCycleException
     * @throws UserNotInDriverRoleException
     * @throws UserNotInPassengerRoleException
     * @throws DuplicateRoleAssignmentException
     * @throws RideNotFoundException
     */
    public function testAcceptingRideByNonDriverThrowsUserNotDriverException()
    {
        $newRide = $this->ride()->getSavedNewRideWithPassengerAndDestination();
        $attemptingDriver = $this->user()->getNewPassenger();

        $this->expectException(UserNotInDriverRoleException::class);
        $this->ride()->acceptRide($newRide, $attemptingDriver);
    }

    /**
     * @throws ActingDriverIsNotAssignedDriverException
     * @throws RideLifeCycleException
     * @throws UserNotInDriverRoleException
     * @throws UserNotInPassengerRoleException
     * @throws DuplicateRoleAssignmentException
     * @throws RideNotFoundException
     */
    public function testMarkRideInProgressByDriver()
    {
        $rideInProgress = $this->ride()->getRideInProgress($this->user()->getNewDriver());
        $rideStatus = $this->ride()->getRideStatus($rideInProgress);

        self::assertTrue(RideEventType::inProgress()->equals($rideStatus));
    }

    /**
     * @throws ActingDriverIsNotAssignedDriverException
     * @throws RideLifeCycleException
     * @throws UserNotInDriverRoleException
     * @throws UserNotInPassengerRoleException
     * @throws DuplicateRoleAssignmentException
     * @throws RideNotFoundException
     */
    public function testMarkingRideInProgressIfNotAcceptedThrowsException()
    {
        $newRide = $this->ride()->getSavedNewRideWithPassengerAndDestination();
        $newDriver = $this->user()->getNewDriver();

        $this->expectException(RideLifeCycleException::class);
        $this->ride()->markRideInProgress($newRide, $newDriver);
    }

    /**
     * @throws ActingDriverIsNotAssignedDriverException
     * @throws RideLifeCycleException
     * @throws UserNotInDriverRoleException
     * @throws UserNotInPassengerRoleException
     * @throws DuplicateRoleAssignmentException
     * @throws RideNotFoundException
     */
    public function testMarkingRideInProgressByNonDriverThrowsException()
    {
        $acceptedRide = $this->ride()->getAcceptedRide();
        $nonDriverUser = $this->user()->getSavedUserWithName('Non', 'Driver');

        $this->expectException(UserNotInDriverRoleException::class);
        $this->ride()->markRideInProgress($acceptedRide, $nonDriverUser);
    }

    /**
     * @throws ActingDriverIsNotAssignedDriverException
     * @throws RideLifeCycleException
     * @throws UserNotInDriverRoleException
     * @throws UserNotInPassengerRoleException
     * @throws DuplicateRoleAssignmentException
     * @throws RideNotFoundException
     */
    public function testMarkingRideInProgressByDriverOtherThanAssignedDriverThrows()
    {
        $acceptedRide = $this->ride()->getAcceptedRide();
        $rogueDriverUser = $this->user()->getNewDriverWithName('Rogue', 'Driver');

        $this->expectException(ActingDriverIsNotAssignedDriverException::class);
        $this->ride()->markRideInProgress($acceptedRide, $rogueDriverUser);
    }

    /**
     * @throws ActingDriverIsNotAssignedDriverException
     * @throws RideLifeCycleException
     * @throws UserNotInDriverRoleException
     * @throws UserNotInPassengerRoleException
     * @throws DuplicateRoleAssignmentException
     * @throws RideNotFoundException
     */
    public function testMarkRideAsCompletedByDriver()
    {
        $newDriver = $this->user()->getNewDriver();
        $rideInProgress = $this->ride()->getRideInProgress($newDriver);

        $completedRide = $this->ride()->markRideCompleted($rideInProgress, $newDriver);
        $rideStatus = $this->ride()->getRideStatus($completedRide);

        self::assertTrue(RideEventType::completed()->equals($rideStatus));
    }

    /**
     * @throws ActingDriverIsNotAssignedDriverException
     * @throws RideLifeCycleException
     * @throws UserNotInDriverRoleException
     * @throws UserNotInPassengerRoleException
     * @throws DuplicateRoleAssignmentException
     * @throws RideNotFoundException
     */
    public function testCompletingRideByDriverOtherThanAssignedDriverThrows()
    {
        $newDriver = $this->user()->getNewDriver();
        $rideInProgress= $this->ride()->getRideInProgress($newDriver);
        $rogueDriver = $this->user()->getNewDriverWithName('Rogue', 'Driver');

        $this->expectException(ActingDriverIsNotAssignedDriverException::class);
        $this->ride()->markRideCompleted($rideInProgress, $rogueDriver);
    }

    /**
     * @throws ActingDriverIsNotAssignedDriverException
     * @throws RideLifeCycleException
     * @throws UserNotInDriverRoleException
     * @throws UserNotInPassengerRoleException
     * @throws DuplicateRoleAssignmentException
     * @throws RideNotFoundException
     */
    public function testCompletingRideIfNotInProgressThrowsException()
    {
        $newDriver = $this->user()->getNewDriver();
        $acceptedRide = $this->ride()->getAcceptedRideWithDriver($newDriver);

        $this->expectException(RideLifeCycleException::class);
        $this->ride()->markRideCompleted($acceptedRide, $newDriver);
    }

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotInPassengerRoleException
     */
    public function testAssignDestinationToRide()
    {
        $newRide = $this->ride()->getSavedNewRideWithPassengerAndDestination();
        $workLocation = $this->location()->getWorkLocation();
        /** @var Ride $rideWithDestination */
        $rideWithDestination = $this->ride()->assignDestinationToRide($newRide, $workLocation);

        self::assertTrue($rideWithDestination->isDestinedFor($workLocation));
    }


    /**
     * @throws ActingDriverIsNotAssignedDriverException
     * @throws DuplicateRoleAssignmentException
     * @throws RideLifeCycleException
     * @throws RideNotFoundException
     * @throws UserNotInDriverRoleException
     * @throws UserNotInPassengerRoleException
     * @throws UserNotFoundException
     */
    public function testPatchRideLifeCycle()
    {
        $driver = $this->user()->getNewDriver();
        $ride = $this->ride()->getSavedNewRideWithPassengerAndDestination();

        $this->assertRidePatchEvent($ride, RideEventType::ACCEPTED_ID, $driver);
        $this->assertRidePatchEvent($ride, RideEventType::IN_PROGRESS_ID, $driver);
        $this->assertRidePatchEvent($ride, RideEventType::COMPLETED_ID, $driver);
    }

    /**
     * @param Ride $ride
     * @param string $eventId
     * @param AppUser $driver
     * @return Ride
     * @throws ActingDriverIsNotAssignedDriverException
     * @throws RideLifeCycleException
     * @throws RideNotFoundException
     * @throws UserNotInDriverRoleException
     * @throws UserNotFoundException
     */
    public function assertRidePatchEvent(Ride $ride, string $eventId, AppUser $driver): Ride
    {
        /** @var Ride $patchedRide */
        $patchedRide = $this->ride()->updateRideByEventId(
            $ride,
            $eventId,
            $driver->getId()->toString()
        );

        self::assertTrue($ride->isDrivenBy($driver));
        self::assertTrue(
            RideEventType::newById(intval($eventId))->equals(
                $this->ride()->getRideStatus($patchedRide)
            )
        );
        return $patchedRide;
    }
}
