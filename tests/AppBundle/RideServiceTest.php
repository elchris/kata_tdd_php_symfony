<?php


namespace Tests\AppBundle;

use AppBundle\Exception\AcceptedRideEventLifeCycleException;
use AppBundle\Exception\NoDriverAssignedException;
use AppBundle\Exception\RideEventLifeCycleException;

class RideServiceTest extends AppTestCase
{
    public function testDuplicatePassengerRoleThrowsException()
    {
        $roleName = 'Passenger';
        $this->setUpDuplicateRoleThrowsException($roleName);
        $this->makePassenger($this->savedUserOne);
    }

    public function testDuplicateDriverRoleThrowsException()
    {
        $roleName = 'Driver';
        $this->setUpDuplicateRoleThrowsException($roleName);
        $this->makeDriver($this->savedUserOne);
    }

    public function testNoLocationDuplication()
    {
        $this->ride()->createRideForUser(
            $this->savedUserOne,
            $this->home,
            $this->work
        );
        $this->ride()->createRideForUser(
            $this->savedUserOne,
            $this->dupeHome,
            $this->dupeWork
        );
        self::assertCount(2, $this->user()->getAllLocations());
    }

    public function testCreateRide()
    {
        $ride = $this->getUserRide();
        self::assertTrue(
            $ride->getDeparture()->equals($this->home)
        );
        self::assertTrue(
            $ride->getDestination()->equals($this->work)
        );
    }

    public function testEventActor()
    {
        $ride = $this->getUserRide();
        $this->ride()->markRideRequested($ride, $this->savedUserOne);
        $status = $this->ride()->getRideStatus($ride);

        self::assertSame(
            $this->savedUserOne->getFirstName(),
            $status->getActor()->getFirstName()
        );
        self::assertSame(
            $this->savedUserOne->getLastName(),
            $status->getActor()->getLastName()
        );
    }

    public function testMarkRideAsRequested()
    {
        $ride = $this->getUserRide();
        $this->ride()->markRideRequested($ride, $this->savedUserOne);

        $status = $this->ride()->getRideStatus($ride);
        self::assertTrue($status->getType()->isRequested());
    }

    public function testDoubleRequestedThrowsException()
    {
        $ride = $this->getUserRide();
        $this->ride()->markRideRequested($ride, $this->savedUserOne);
        self::expectException(RideEventLifeCycleException::class);
        $this->ride()->markRideRequested($ride, $this->savedUserOne);
    }

    public function testMarkRideAsAcceptedByDriver()
    {
        $ride = $this->makeRideForPassengerAndDriver();
        $status = $this->ride()->getRideStatus($ride);
        self::assertTrue($status->getType()->isAccepted());
        self::assertSame(
            $status->getActor()->getFirstName(),
            $this->savedUserTwo->getFirstName()
        );
    }

    public function testDriverAssignedToRide()
    {
        $ride = $this->makeRideForPassengerAndDriver();
        self::assertSame(
            $this->savedUserTwo->getFirstName(),
            $ride->getDriver()->getFirstName()
        );
        self::assertSame(
            $this->savedUserTwo->getLastName(),
            $ride->getDriver()->getLastName()
        );
    }

    public function testDoubleAssignDriverThrows()
    {
        $ride = $this->makeRideForPassengerAndDriver();
        self::expectException(RideEventLifeCycleException::class);
        self::expectException(AcceptedRideEventLifeCycleException::class);
        self::expectExceptionMessage('This ride has already been processed: Accepted');
        $this->ride()->markRideAsAcceptedByDriver(
            $ride,
            $this->savedUserTwo
        );
    }

    public function testUnAssignedDriverThrows()
    {
        $ride = $this->getUserRide();
        self::expectException(NoDriverAssignedException::class);
        $ride->getDriver();
    }
}
