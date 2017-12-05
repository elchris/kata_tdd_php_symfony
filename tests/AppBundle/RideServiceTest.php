<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppLocation;
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
        self::assertNotEmpty($newRide->getId());
    }

    /**
     * 1) the user of the Ride must be a passenger
     * 2) the Ride must not have already been requested
     */

    public function testRideUserNotPassengerThrowsRoleException()
    {
        $notPassengerUser = $this->getSavedUser();
        self::assertFalse($this->isPassenger($notPassengerUser));
        $departure = $this->getSavedHomeLocation();

        $this->expectException(UserNotPassengerException::class);

        $this->getNewRide($notPassengerUser, $departure);
    }

    public function testGetRideStatusIsRequestedWhenNew()
    {
        $newRide = $this->getSavedNewRideWithPassengerAndDestination();
        $rideStatus = $this->getRideStatus($newRide);

        self::assertTrue(RideEventType::requested()->equals($rideStatus));
    }

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

    public function testAcceptingNonRequestedRideThrowsException()
    {
        $newRide = $this->getSavedNewRideWithPassengerAndDestination();
        $winningDriver = $this->getNewDriverWithName('Winning', 'Driver');
        $losingDriver = $this->getNewDriverWithName('Losing', 'Driver');
        $this->acceptRide($newRide, $winningDriver);

        $this->expectException(RideLifeCycleException::class);
        $this->acceptRide($newRide, $losingDriver);
    }

    public function testAcceptingRideByNonDriverThrowsUserNotDriverException()
    {
        $newRide = $this->getSavedNewRideWithPassengerAndDestination();
        $attemptingDriver = $this->getNewPassenger();

        $this->expectException(UserNotInDriverRoleException::class);
        $this->acceptRide($newRide, $attemptingDriver);
    }

    public function testMarkRideInProgressByDriver()
    {
        $rideInProgress = $this->getRideInProgress($this->getNewDriver());
        $rideStatus = $this->getRideStatus($rideInProgress);

        self::assertTrue(RideEventType::inProgress()->equals($rideStatus));
    }

    public function testMarkingRideInProgressIfNotAcceptedThrowsException()
    {
        $newRide = $this->getSavedNewRideWithPassengerAndDestination();
        $newDriver = $this->getNewDriver();

        $this->expectException(RideLifeCycleException::class);
        $this->markRideInProgress($newRide, $newDriver);
    }

    public function testMarkingRideInProgressByNonDriverThrowsException()
    {
        $acceptedRide = $this->getAcceptedRide();
        $nonDriverUser = $this->getSavedUserWithName('Non', 'Driver');

        $this->expectException(UserNotInDriverRoleException::class);
        $this->markRideInProgress($acceptedRide, $nonDriverUser);
    }

    public function testMarkingRideInProgressByDriverOtherThanAssignedDriverThrows()
    {
        $acceptedRide = $this->getAcceptedRide();
        $rogueDriverUser = $this->getNewDriverWithName('Rogue', 'Driver');

        $this->expectException(ActingDriverIsNotAssignedDriverException::class);
        $this->markRideInProgress($acceptedRide, $rogueDriverUser);
    }

    public function testMarkRideAsCompletedByDriver()
    {
        $newDriver = $this->getNewDriver();
        $rideInProgress = $this->getRideInProgress($newDriver);

        $completedRide = $this->markRideCompleted($rideInProgress, $newDriver);
        $rideStatus = $this->getRideStatus($completedRide);

        self::assertTrue(RideEventType::completed()->equals($rideStatus));
    }

    public function testCompletingRideByDriverOtherThanAssignedDriverThrows()
    {
        $newDriver = $this->getNewDriver();
        $rideInProgress= $this->getRideInProgress($newDriver);
        $rogueDriver = $this->getNewDriverWithName('Rogue', 'Driver');

        $this->expectException(ActingDriverIsNotAssignedDriverException::class);
        $this->markRideCompleted($rideInProgress, $rogueDriver);
    }

    public function testCompletingRideIfNotInProgressThrowsException()
    {
        $newDriver = $this->getNewDriver();
        $acceptedRide = $this->getAcceptedRideWithDriver($newDriver);

        $this->expectException(RideLifeCycleException::class);
        $this->markRideCompleted($acceptedRide, $newDriver);
    }

    /**
     * @return Ride
     */
    protected function getSavedNewRideWithPassengerAndDestination()
    {
        $passenger = $this->getSavedUser();
        $this->makeUserPassenger($passenger);

        $departure = $this->getSavedHomeLocation();

        /** @var Ride $newRide */
        $newRide = $this->getNewRide(
            $passenger,
            $departure
        );

        return $newRide;
    }

    /**
     * @param AppUser $driver
     * @return Ride
     */
    protected function getRideInProgress(AppUser $driver)
    {
        $newRide = $this->getSavedNewRideWithPassengerAndDestination();
        $acceptedRide = $this->acceptRide($newRide, $driver);
        return $this->markRideInProgress($acceptedRide, $driver);
    }

    /**
     * @return Ride
     */
    protected function getAcceptedRide()
    {
        $newDriver = $this->getNewDriver();
        return $this->getAcceptedRideWithDriver($newDriver);
    }

    protected function getAcceptedRideWithDriver(AppUser $driver)
    {
        $newRide = $this->getSavedNewRideWithPassengerAndDestination();
        return $this->acceptRide($newRide, $driver);
    }

    /**
     * @param $rideInProgress
     * @param $newDriver
     * @return Ride
     */
    protected function markRideCompleted(Ride $rideInProgress, AppUser $newDriver)
    {
        return $this->rideService->markRideCompleted($rideInProgress, $newDriver);
    }

    /**
     * @param $completedRide
     * @return RideEventType
     */
    protected function getRideStatus(Ride $completedRide)
    {
        return $this->rideService->getRideStatus($completedRide);
    }

    /**
     * @param $acceptedRide
     * @param $nonDriverUser
     * @return Ride
     */
    protected function markRideInProgress(Ride $acceptedRide, AppUser $nonDriverUser)
    {
        return $this->rideService->markRideInProgress($acceptedRide, $nonDriverUser);
    }

    /**
     * @param $newRide
     * @param $losingDriver
     * @return Ride
     */
    protected function acceptRide(Ride $newRide, AppUser $losingDriver)
    {
        return $this->rideService->acceptRide($newRide, $losingDriver);
    }

    /**
     * @param $notPassengerUser
     * @param $departure
     * @return Ride
     */
    protected function getNewRide(AppUser $notPassengerUser, AppLocation $departure)
    {
        return $this->rideService->newRide($notPassengerUser, $departure);
    }
}
