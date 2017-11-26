<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEventType;
use AppBundle\Exception\ActingDriverIsNotAssignedDriverException;
use AppBundle\Exception\RideLifeCycleException;
use AppBundle\Exception\UserNotInDriverRoleException;
use AppBundle\Exception\UserNotPassengerException;
use AppBundle\Service\RideService;

class RideServiceTest extends AppTestCase
{
    /** @var  RideService */
    private $rideService;

    public function setUp()
    {
        parent::setUp();
        $this->rideService = new RideService(
            $this->rideRepository,
            $this->rideEventRepository
        );
    }

    public function testCreateRide()
    {
        $newRide = $this->getSavedNewRideWithPassengerAndDestination();

        self::assertInstanceOf(Ride::class, $newRide);
        self::assertGreaterThan(0, $newRide->getId());
    }

    /**
     * 1) the user of the Ride must be a passenger
     * 2) the Ride must not have already been requested
     */

    public function testRideUserNotPassengerThrowsRoleException()
    {
        $notPassengerUser = $this->getSavedUser();
        self::assertFalse($this->userService->isPassenger($notPassengerUser));
        $departure = $this->getSavedHomeLocation();

        $this->expectException(UserNotPassengerException::class);

        $this->rideService->newRide($notPassengerUser, $departure);
    }

    public function testGetRideStatusIsRequestedWhenNew()
    {
        $newRide = $this->getSavedNewRideWithPassengerAndDestination();
        $rideStatus = $this->rideService->getRideStatus($newRide);

        self::assertTrue(RideEventType::requested()->equals($rideStatus));
    }

    public function testAcceptRideByProspectiveDriver()
    {
        //verify that the user is a driver role
        //verify that the ride is in requested status
        //mark the ride as accepted

        $newRide = $this->getSavedNewRideWithPassengerAndDestination();
        $newDriver = $this->getNewDriver();

        $acceptedRide = $this->rideService->acceptRide($newRide, $newDriver);
        $rideStatus = $this->rideService->getRideStatus($newRide);

        self::assertTrue(RideEventType::accepted()->equals($rideStatus));
        self::assertTrue($acceptedRide->isDrivenBy($newDriver));
    }

    public function testAcceptingNonRequestedRideThrowsException()
    {
        $newRide = $this->getSavedNewRideWithPassengerAndDestination();
        $winningDriver = $this->getNewDriverWithName('Winning', 'Driver');
        $losingDriver = $this->getNewDriverWithName('Losing', 'Driver');

        $this->rideService->acceptRide($newRide, $winningDriver);
        $this->expectException(RideLifeCycleException::class);

        $this->rideService->acceptRide($newRide, $losingDriver);
    }

    public function testAcceptingRideByNonDriverThrowsUserNotDriverException()
    {
        $newRide = $this->getSavedNewRideWithPassengerAndDestination();
        $attemptingDriver = $this->getSavedUser();
        $this->userService->makeUserPassenger($attemptingDriver);

        $this->expectException(UserNotInDriverRoleException::class);

        $this->rideService->acceptRide($newRide, $attemptingDriver);
    }

    public function testMarkRideInProgressByDriver()
    {
        $newDriver = $this->getNewDriver();
        $rideInProgress = $this->getRideInProgress($newDriver);
        $rideStatus = $this->rideService->getRideStatus($rideInProgress);

        self::assertTrue(RideEventType::inProgress()->equals($rideStatus));
    }

    public function testMarkingRideInProgressIfNotAcceptedThrowsException()
    {
        $newRide = $this->getSavedNewRideWithPassengerAndDestination();
        $newDriver = $this->getNewDriver();

        $this->expectException(RideLifeCycleException::class);
        $this->rideService->markRideInProgress($newRide, $newDriver);
    }

    public function testMarkingRideInProgressByNonDriverThrowsException()
    {
        $acceptedRide = $this->getAcceptedRide();
        $nonDriverUser = $this->getSavedUserWithName('Non', 'Driver');

        $this->expectException(UserNotInDriverRoleException::class);
        $this->rideService->markRideInProgress($acceptedRide, $nonDriverUser);
    }

    public function testMarkingRideInProgressByDriverOtherThanAssignedDriverThrows()
    {
        $acceptedRide = $this->getAcceptedRide();
        $rogueDriverUser = $this->getNewDriverWithName('Rogue', 'Driver');

        $this->expectException(ActingDriverIsNotAssignedDriverException::class);
        $this->rideService->markRideInProgress($acceptedRide, $rogueDriverUser);
    }

    public function testMarkRideAsCompletedByDriver()
    {
        $newDriver = $this->getNewDriver();
        $rideInProgress = $this->getRideInProgress($newDriver);

        $completedRide = $this->rideService->markRideCompleted($rideInProgress, $newDriver);
        $rideStatus = $this->rideService->getRideStatus($completedRide);

        self::assertTrue(RideEventType::completed()->equals($rideStatus));
    }

    public function testCompletingRideByDriverOtherThanAssignedDriverThrows()
    {
        $newDriver = $this->getNewDriver();
        $rideInProgress= $this->getRideInProgress($newDriver);
        $rogueDriver = $this->getNewDriverWithName('Rogue', 'Driver');

        $this->expectException(ActingDriverIsNotAssignedDriverException::class);
        $this->rideService->markRideCompleted($rideInProgress, $rogueDriver);
    }

    /**
     * @return Ride
     */
    protected function getSavedNewRideWithPassengerAndDestination()
    {
        $passenger = $this->getSavedUser();
        $this->userService->makeUserPassenger($passenger);

        $departure = $this->getSavedHomeLocation();

        /** @var Ride $newRide */
        $newRide = $this->rideService->newRide(
            $passenger,
            $departure
        );

        return $newRide;
    }

    /**
     * @return \AppBundle\Entity\AppUser
     */
    protected function getNewDriver()
    {
        return $this->getNewDriverWithName('new', 'driver');
    }

    protected function getNewDriverWithName($first, $last)
    {
        $driver = $this->getSavedUserWithName($first, $last);
        $this->userService->makeUserDriver($driver);
        return $driver;
    }

    /**
     * @param AppUser $driver
     * @return Ride
     */
    protected function getRideInProgress(AppUser $driver)
    {
        $newRide = $this->getSavedNewRideWithPassengerAndDestination();
        $acceptedRide = $this->rideService->acceptRide($newRide, $driver);

        $rideInProgress = $this->rideService->markRideInProgress($acceptedRide, $driver);

        return $rideInProgress;
    }

    /**
     * @return Ride
     */
    protected function getAcceptedRide()
    {
        $newDriver = $this->getNewDriver();
        $newRide = $this->getSavedNewRideWithPassengerAndDestination();
        $acceptedRide = $this->rideService->acceptRide($newRide, $newDriver);

        return $acceptedRide;
    }
}
