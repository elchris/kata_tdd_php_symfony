<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEventType;
use AppBundle\RideEventLifeCycleException;
use AppBundle\RoleLifeCycleException;
use AppBundle\UnassignedDriverException;

/**
 * Consult ride-hailing.svg to digest some key application concepts.
 *
 * Consult Kata-Tasks.rtf to get an idea of the various tests you'll be writing
 * and help shape your sequencing.
 *
 * With this said, you do not have to follow the sequencing outlined.
 * In fact, you will likely arrive at a more optimal sequencing.
 *
 *
 * Class AppTest
 * @package Tests\AppBundle
 */
class AppTest extends AppTestCase
{
    /** @var  AppLocation */
    private $home;
    /** @var  AppUser */
    private $userOne;
    /** @var  AppUser */
    private $userTwo;

    public function setUp()
    {
        parent::setUp();
        $this->appService->newUser('Chris', 'Holland');
        $this->appService->newUser('Scott', 'Sims');
        /** @var AppUser $user */
        $this->userTwo = $this->appService->getUserById(2);
        $this->userOne = $this->appService->getUserById(1);

        $this->home = $this->appService->getLocation(
            37.773160,
            -122.432444
        );

        $this->save(AppRole::asPassenger());
        $this->save(AppRole::asDriver());
        $this->save(RideEventType::asRequested());
        $this->save(RideEventType::asAccepted());
        $this->save(RideEventType::inProgress());
        $this->save(RideEventType::asCancelled());
        $this->save(RideEventType::asCompleted());
        $this->save(RideEventType::asRejected());
    }

    public function testCreateAndRetrieveUser()
    {
        self::assertEquals('Scott', $this->userTwo->getFirstName());
        self::assertEquals('Chris', $this->userOne->getFirstName());
    }

    public function testMakeUserPassenger()
    {
        $this->appService->assignRoleToUser($this->userOne, AppRole::asPassenger());
        self::assertTrue($this->appService->isUserPassenger($this->userOne));
    }

    public function testMakerUserDriver()
    {
        $this->appService->assignRoleToUser($this->userOne, AppRole::asDriver());
        self::assertTrue($this->appService->isUserDriver($this->userOne));
    }

    public function testUserHasBothRoles()
    {
        $this->appService->assignRoleToUser($this->userOne, AppRole::asPassenger());
        $this->appService->assignRoleToUser($this->userOne, AppRole::asDriver());
        self::assertTrue($this->appService->isUserPassenger($this->userOne));
        self::assertTrue($this->appService->isUserDriver($this->userOne));
    }

    public function testDuplicateRoleAssignmentThrowsException()
    {
        $this->appService->assignRoleToUser($this->userOne, AppRole::asPassenger());
        $this->expectException(RoleLifeCycleException::class);
        $this->appService->assignRoleToUser($this->userOne, AppRole::asPassenger());
    }
    /*
     * home: 37.773160, -122.432444
     * work: 37.7721718,-122.4310872
     */
    public function testGetOrCreateLocation()
    {
        self::assertEquals(37.773160, $this->home->getLat(), 0.00000001);
        self::assertEquals(-122.432444, $this->home->getLong(), 0.00000001);
    }

    public function testCreateRideForPassengerAndDeparture()
    {
        $firstRide = $this->makePassengerRide();
        self::assertEquals($this->userOne->getFullName(), $firstRide->getPassenger()->getFullName());
        self::assertTrue($this->home->equals($firstRide->getDeparture()));
    }

    public function testMarkRideAsRequested()
    {
        $ride = $this->makePassengerRide();
        $this->appService->passengerMarkRideAs($ride, RideEventType::asRequested());

        $rideStatus = $this->appService->getRideStatus($ride);
        self::assertEquals("Requested", $rideStatus->getType()->getName());
    }

    public function testCheckRideStatus()
    {
        $ride = $this->makePassengerRide();
        $this->appService->passengerMarkRideAs($ride, RideEventType::asRequested());

        self::assertTrue($this->appService->isRide($ride, RideEventType::asRequested()));
    }

    public function testAssignDriverToRide()
    {
        $ride = $this->makePassengerRide();
        $this->appService->passengerMarkRideAs($ride, RideEventType::asRequested());

        $this->assignDriverToRide($ride);

        self::assertTrue($this->appService->isUserDriver($ride->getDriver()));
    }

    public function testRideEventLifeCycles()
    {
        $ride = $this->makePassengerRide();
        $this->appService->passengerMarkRideAs($ride, RideEventType::asRequested());
        self::assertTrue($this->appService->isRide($ride, RideEventType::asRequested()));

        $this->assignDriverToRide($ride);

        $this->appService->driverMarkRideAs($ride, RideEventType::asAccepted());
        self::assertTrue($this->appService->isRide($ride, RideEventType::asAccepted()));

        $this->appService->driverMarkRideAs($ride, RideEventType::inProgress());
        self::assertTrue($this->appService->isRide($ride, RideEventType::inProgress()));
        
        $this->appService->driverMarkRideAs($ride, RideEventType::asCompleted());
        self::assertTrue($this->appService->isRide($ride, RideEventType::asCompleted()));

        $this->appService->driverMarkRideAs($ride, RideEventType::asRejected());
        self::assertTrue($this->appService->isRide($ride, RideEventType::asRejected()));
    }

    public function testOutOfSequenceRequestedEventThrows()
    {
        $ride = $this->makePassengerRide();
        $this->appService->passengerMarkRideAs($ride, RideEventType::asRequested());
        $this->expectException(RideEventLifeCycleException::class);
        $this->appService->passengerMarkRideAs($ride, RideEventType::asRequested());
    }

    public function testUnassignedDriverThrows()
    {
        $ride = $this->makePassengerRide();
        $this->expectException(UnassignedDriverException::class);
        $this->appService->driverMarkRideAs($ride, RideEventType::asAccepted());

    }

    public function testOutOfSequenceAcceptedEventThrows()
    {
        $ride = $this->makePassengerRide();
        $this->assignDriverToRide($ride);
        $this->expectException(RideEventLifeCycleException::class);
        $this->appService->driverMarkRideAs($ride, RideEventType::asAccepted());
    }

    public function testOutOfSequenceInProgressEventThrows()
    {
        $ride = $this->makePassengerRide();
        $this->appService->passengerMarkRideAs($ride, RideEventType::asRequested());
        $this->assignDriverToRide($ride);
        $this->expectException(RideEventLifeCycleException::class);
        $this->appService->driverMarkRideAs($ride, RideEventType::inProgress());
    }

    public function testOutOfSequenceCancelledEventThrows()
    {
        $ride = $this->getAcceptedPassengerRide();
        $this->appService->driverMarkRideAs($ride, RideEventType::inProgress());
        self::expectException(RideEventLifeCycleException::class);
        $this->appService->driverMarkRideAs($ride, RideEventType::asCancelled());
    }

    public function testCancelledEventWorks()
    {
        $ride = $this->getAcceptedPassengerRide();
        $this->appService->driverMarkRideAs($ride, RideEventType::asCancelled());
        self::assertTrue($this->appService->isRide($ride, RideEventType::asCancelled()));
    }

    public function testOutOfSequenceCompletedThrows()
    {
        $ride = $this->getAcceptedPassengerRide();
        $this->expectException(RideEventLifeCycleException::class);
        $this->appService->driverMarkRideAs($ride, RideEventType::asCompleted());
    }


    /**
     * @return Ride
     */
    private function makePassengerRide()
    {
        $this->appService->assignRoleToUser($this->userOne, AppRole::asPassenger());
        $this->appService->createRide(
            $this->userOne,
            $this->home
        );

        /** @var Ride[] $ridesForUser */
        $ridesForUser = $this->appService->getRidesForPassenger($this->userOne);
        self::assertCount(1, $ridesForUser);
        $firstRide = $ridesForUser[0];

        return $firstRide;
    }

    /**
     * @param $ride
     */
    private function assignDriverToRide($ride)
    {
        $this->appService->assignRoleToUser($this->userTwo, AppRole::asDriver());
        $this->appService->assignDriverToRide($ride, $this->userTwo);
    }

    /**
     * @return Ride
     */
    private function getAcceptedPassengerRide()
    {
        $ride = $this->makePassengerRide();
        $this->appService->passengerMarkRideAs($ride, RideEventType::asRequested());
        $this->assignDriverToRide($ride);
        $this->appService->driverMarkRideAs($ride, RideEventType::asAccepted());

        return $ride;
    }
}
