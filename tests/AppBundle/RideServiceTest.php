<?php

namespace Tests\AppBundle;

use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEventType;
use AppBundle\Exception\RideLifeCycleException;
use AppBundle\Exception\UserNotDriverException;
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
        $driver = $this->getSavedUser();
        $this->userService->makeUserDriver($driver);

        $acceptedRide = $this->rideService->acceptRide($newRide, $driver);
        $rideStatus = $this->rideService->getRideStatus($newRide);

        self::assertTrue(RideEventType::accepted()->equals($rideStatus));
        self::assertSame($driver->getLastName(), $acceptedRide->getDriver()->getLastName());
    }

    public function testAcceptingNonRequestedRideThrowsException()
    {
        $newRide = $this->getSavedNewRideWithPassengerAndDestination();
        $winningDriver = $this->getSavedUser();
        $this->userService->makeUserDriver($winningDriver);

        $losingDriver = $this->getSavedUserWithName('Losing', 'Driver');
        $this->userService->makeUserDriver($losingDriver);

        $this->rideService->acceptRide($newRide, $winningDriver);
        $this->expectException(RideLifeCycleException::class);

        $this->rideService->acceptRide($newRide, $losingDriver);
    }

    public function testAcceptingRideByNonDriverThrowsUserNotDriverException()
    {
        $newRide = $this->getSavedNewRideWithPassengerAndDestination();
        $attemptingDriver = $this->getSavedUser();
        $this->userService->makeUserPassenger($attemptingDriver);

        $this->expectException(UserNotDriverException::class);

        $this->rideService->acceptRide($newRide, $attemptingDriver);
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
}
