<?php

namespace Tests\AppBundle;

use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEventType;
use AppBundle\Exception\ActingDriverIsNotAssignedDriverException;
use AppBundle\Exception\RideLifeCycleException;
use AppBundle\Exception\UserNotInDriverRoleException;
use AppBundle\Exception\UserNotPassengerException;

class RideServiceTest extends AppTestCase
{
    /**
     * @throws UserNotPassengerException
     */
    public function testCreateRide()
    {
        $newRide = $this->getSavedNewRideWithPassengerAndDestination();

        self::assertInstanceOf(Ride::class, $newRide);
        self::assertNotEmpty($newRide->getId());
    }

    /**
     * 1) the user of the Ride must be a passenger
     * 2) the Ride must not have already been requested
     * @throws UserNotPassengerException
     */

    public function testRideUserNotPassengerThrowsRoleException()
    {
        $notPassengerUser = $this->getSavedUser();
        self::assertFalse($this->isPassenger($notPassengerUser));
        $departure = $this->getSavedHomeLocation();

        $this->expectException(UserNotPassengerException::class);

        $this->getNewRide($notPassengerUser, $departure);
    }

    /**
     * @throws UserNotPassengerException
     */
    public function testGetRideStatusIsRequestedWhenNew()
    {
        $newRide = $this->getSavedNewRideWithPassengerAndDestination();
        $rideStatus = $this->getRideStatus($newRide);

        self::assertTrue(RideEventType::requested()->equals($rideStatus));
    }

    /**
     * @throws UserNotPassengerException
     */
    public function testAcceptRideByProspectiveDriver()
    {
        //verify that the user is a driver role
        //verify that the ride is in requested status
        //mark the ride as accepted

        $newRide = $this->getSavedNewRideWithPassengerAndDestination();
        $newDriver = $this->getNewDriver();

        $acceptedRide = $this->acceptRide($newRide, $newDriver);
        $rideStatus = $this->getRideStatus($newRide);

        self::assertTrue(RideEventType::accepted()->equals($rideStatus));
        self::assertTrue($acceptedRide->isDrivenBy($newDriver));
    }

    /**
     * @throws UserNotPassengerException
     */
    public function testAcceptingNonRequestedRideThrowsException()
    {
        $newRide = $this->getSavedNewRideWithPassengerAndDestination();
        $winningDriver = $this->getNewDriverWithName('Winning', 'Driver');
        $losingDriver = $this->getNewDriverWithName('Losing', 'Driver');
        $this->acceptRide($newRide, $winningDriver);

        $this->expectException(RideLifeCycleException::class);
        $this->acceptRide($newRide, $losingDriver);
    }

    /**
     * @throws UserNotPassengerException
     */
    public function testAcceptingRideByNonDriverThrowsUserNotDriverException()
    {
        $newRide = $this->getSavedNewRideWithPassengerAndDestination();
        $attemptingDriver = $this->getNewPassenger();

        $this->expectException(UserNotInDriverRoleException::class);
        $this->acceptRide($newRide, $attemptingDriver);
    }

    /**
     * @throws UserNotPassengerException
     */
    public function testMarkRideInProgressByDriver()
    {
        $rideInProgress = $this->getRideInProgress($this->getNewDriver());
        $rideStatus = $this->getRideStatus($rideInProgress);

        self::assertTrue(RideEventType::inProgress()->equals($rideStatus));
    }

    /**
     * @throws UserNotPassengerException
     */
    public function testMarkingRideInProgressIfNotAcceptedThrowsException()
    {
        $newRide = $this->getSavedNewRideWithPassengerAndDestination();
        $newDriver = $this->getNewDriver();

        $this->expectException(RideLifeCycleException::class);
        $this->markRideInProgress($newRide, $newDriver);
    }

    /**
     * @throws UserNotPassengerException
     */
    public function testMarkingRideInProgressByNonDriverThrowsException()
    {
        $acceptedRide = $this->getAcceptedRide();
        $nonDriverUser = $this->getSavedUserWithName('Non', 'Driver');

        $this->expectException(UserNotInDriverRoleException::class);
        $this->markRideInProgress($acceptedRide, $nonDriverUser);
    }

    /**
     * @throws UserNotPassengerException
     */
    public function testMarkingRideInProgressByDriverOtherThanAssignedDriverThrows()
    {
        $acceptedRide = $this->getAcceptedRide();
        $rogueDriverUser = $this->getNewDriverWithName('Rogue', 'Driver');

        $this->expectException(ActingDriverIsNotAssignedDriverException::class);
        $this->markRideInProgress($acceptedRide, $rogueDriverUser);
    }

    /**
     * @throws UserNotPassengerException
     */
    public function testMarkRideAsCompletedByDriver()
    {
        $newDriver = $this->getNewDriver();
        $rideInProgress = $this->getRideInProgress($newDriver);

        $completedRide = $this->markRideCompleted($rideInProgress, $newDriver);
        $rideStatus = $this->getRideStatus($completedRide);

        self::assertTrue(RideEventType::completed()->equals($rideStatus));
    }

    /**
     * @throws UserNotPassengerException
     */
    public function testCompletingRideByDriverOtherThanAssignedDriverThrows()
    {
        $newDriver = $this->getNewDriver();
        $rideInProgress= $this->getRideInProgress($newDriver);
        $rogueDriver = $this->getNewDriverWithName('Rogue', 'Driver');

        $this->expectException(ActingDriverIsNotAssignedDriverException::class);
        $this->markRideCompleted($rideInProgress, $rogueDriver);
    }

    /**
     * @throws UserNotPassengerException
     */
    public function testCompletingRideIfNotInProgressThrowsException()
    {
        $newDriver = $this->getNewDriver();
        $acceptedRide = $this->getAcceptedRideWithDriver($newDriver);

        $this->expectException(RideLifeCycleException::class);
        $this->markRideCompleted($acceptedRide, $newDriver);
    }
}
