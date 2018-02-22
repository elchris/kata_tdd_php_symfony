<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEventType;
use AppBundle\Exception\ActingDriverIsNotAssignedDriverException;
use AppBundle\Exception\DuplicateRoleAssignmentException;
use AppBundle\Exception\RideLifeCycleException;
use AppBundle\Exception\RideNotFoundException;
use AppBundle\Exception\UnauthorizedOperationException;
use AppBundle\Exception\UserNotInDriverRoleException;
use AppBundle\Exception\UserNotInPassengerRoleException;

class RideServiceTest extends AppTestCase
{
    /**
     * @throws UserNotInPassengerRoleException
     * @throws DuplicateRoleAssignmentException
     * @throws UnauthorizedOperationException
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
     * @throws UnauthorizedOperationException
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
        self::assertFalse($notPassengerUser->userHasRole(AppRole::passenger()));
        $departure = $this->location()->getSavedHomeLocation();

        $this->verifyExceptionWithMessage(
            UserNotInPassengerRoleException::class,
            UserNotInPassengerRoleException::MESSAGE
        );

        $this->ride()->getNewRide($notPassengerUser, $departure);
    }

    /**
     * @throws UserNotInPassengerRoleException
     * @throws DuplicateRoleAssignmentException
     * @throws RideNotFoundException
     * @throws UnauthorizedOperationException
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
     * @throws UnauthorizedOperationException
     */
    public function testAcceptRideByProspectiveDriver()
    {
        $newRide = $this->ride()->getSavedNewRideWithPassengerAndDestination();
        $newDriver = $this->user()->getNewDriver();

        $acceptedRide = $this->ride()->acceptRide($newRide, $newDriver);
        $rideStatus = $this->ride()->getRideStatus($acceptedRide);

        self::assertTrue(RideEventType::accepted()->equals($rideStatus));
        self::assertTrue($acceptedRide->isDrivenBy($newDriver));
    }

    /**
     * @throws RideLifeCycleException
     * @throws UserNotInDriverRoleException
     * @throws UserNotInPassengerRoleException
     * @throws DuplicateRoleAssignmentException
     * @throws RideNotFoundException
     * @throws UnauthorizedOperationException
     */
    public function testAcceptingNonRequestedRideThrowsException()
    {
        $newRide = $this->ride()->getSavedNewRideWithPassengerAndDestination();
        $winningDriver = $this->user()->getNewDriverWithName('Winning', 'Driver');
        $losingDriver = $this->user()->getNewDriverWithName('Losing', 'Driver');
        $this->ride()->acceptRide($newRide, $winningDriver);

        $this->verifyExceptionWithMessage(
            RideLifeCycleException::class,
            RideLifeCycleException::MESSAGE
        );
        $this->ride()->acceptRide($newRide, $losingDriver);
    }

    /**
     * @throws RideLifeCycleException
     * @throws UserNotInDriverRoleException
     * @throws UserNotInPassengerRoleException
     * @throws DuplicateRoleAssignmentException
     * @throws RideNotFoundException
     * @throws UnauthorizedOperationException
     */
    public function testAcceptingRideByNonDriverThrowsUserNotDriverException()
    {
        $newRide = $this->ride()->getSavedNewRideWithPassengerAndDestination();
        $attemptingDriver = $this->user()->getNewPassenger();

        $this->verifyExceptionWithMessage(
            UserNotInDriverRoleException::class,
            UserNotInDriverRoleException::MESSAGE
        );
        $this->ride()->acceptRide($newRide, $attemptingDriver);
    }

    /**
     * @throws ActingDriverIsNotAssignedDriverException
     * @throws RideLifeCycleException
     * @throws UserNotInDriverRoleException
     * @throws UserNotInPassengerRoleException
     * @throws DuplicateRoleAssignmentException
     * @throws RideNotFoundException
     * @throws UnauthorizedOperationException
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
     * @throws UnauthorizedOperationException
     */
    public function testMarkingRideInProgressIfNotAcceptedThrowsException()
    {
        $newRide = $this->ride()->getSavedNewRideWithPassengerAndDestination();
        $newDriver = $this->user()->getNewDriver();

        $this->verifyExceptionWithMessage(
            RideLifeCycleException::class,
            RideLifeCycleException::MESSAGE
        );
        $this->ride()->markRideInProgress($newRide, $newDriver);
    }

    /**
     * @throws ActingDriverIsNotAssignedDriverException
     * @throws RideLifeCycleException
     * @throws UserNotInDriverRoleException
     * @throws UserNotInPassengerRoleException
     * @throws DuplicateRoleAssignmentException
     * @throws RideNotFoundException
     * @throws UnauthorizedOperationException
     */
    public function testMarkingRideInProgressByNonDriverThrowsException()
    {
        $acceptedRide = $this->ride()->getAcceptedRide();
        $nonDriverUser = $this->user()->getSavedUserWithName('Non', 'Driver');

        $this->verifyExceptionWithMessage(
            UserNotInDriverRoleException::class,
            UserNotInDriverRoleException::MESSAGE
        );
        $this->ride()->markRideInProgress($acceptedRide, $nonDriverUser);
    }

    /**
     * @throws ActingDriverIsNotAssignedDriverException
     * @throws RideLifeCycleException
     * @throws UserNotInDriverRoleException
     * @throws UserNotInPassengerRoleException
     * @throws DuplicateRoleAssignmentException
     * @throws RideNotFoundException
     * @throws UnauthorizedOperationException
     */
    public function testMarkingRideInProgressByDriverOtherThanAssignedDriverThrows()
    {
        $acceptedRide = $this->ride()->getAcceptedRide();
        $rogueDriverUser = $this->user()->getNewDriverWithName('Rogue', 'Driver');

        $this->verifyExceptionWithMessage(
            ActingDriverIsNotAssignedDriverException::class,
            ActingDriverIsNotAssignedDriverException::MESSAGE
        );
        $this->ride()->markRideInProgress($acceptedRide, $rogueDriverUser);
    }

    /**
     * @throws ActingDriverIsNotAssignedDriverException
     * @throws RideLifeCycleException
     * @throws UserNotInDriverRoleException
     * @throws UserNotInPassengerRoleException
     * @throws DuplicateRoleAssignmentException
     * @throws RideNotFoundException
     * @throws UnauthorizedOperationException
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
     * @throws UnauthorizedOperationException
     */
    public function testCompletingRideByDriverOtherThanAssignedDriverThrows()
    {
        $newDriver = $this->user()->getNewDriver();
        $rideInProgress= $this->ride()->getRideInProgress($newDriver);
        $rogueDriver = $this->user()->getNewDriverWithName('Rogue', 'Driver');

        $this->verifyExceptionWithMessage(
            ActingDriverIsNotAssignedDriverException::class,
            ActingDriverIsNotAssignedDriverException::MESSAGE
        );
        $this->ride()->markRideCompleted($rideInProgress, $rogueDriver);
    }

    /**
     * @throws ActingDriverIsNotAssignedDriverException
     * @throws RideLifeCycleException
     * @throws UserNotInDriverRoleException
     * @throws UserNotInPassengerRoleException
     * @throws DuplicateRoleAssignmentException
     * @throws RideNotFoundException
     * @throws UnauthorizedOperationException
     */
    public function testCompletingRideIfNotInProgressThrowsException()
    {
        $newDriver = $this->user()->getNewDriver();
        $acceptedRide = $this->ride()->getAcceptedRideWithDriver($newDriver);

        $this->verifyExceptionWithMessage(
            RideLifeCycleException::class,
            RideLifeCycleException::MESSAGE
        );
        $this->ride()->markRideCompleted($acceptedRide, $newDriver);
    }

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotInPassengerRoleException
     * @throws UnauthorizedOperationException
     */
    public function testAssignDestinationToRide()
    {
        $newRide = $this->ride()->getSavedNewRideWithPassengerAndDestination();
        $workLocation = $this->location()->getWorkLocation();
        /** @var Ride $rideWithDestination */
        $rideWithDestination = $this->ride()->assignDestinationToRide($newRide, $workLocation);

        self::assertTrue($rideWithDestination->isDestinedFor($workLocation));
    }
}
