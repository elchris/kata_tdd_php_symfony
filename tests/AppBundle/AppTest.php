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

    /** @var  AppUser */
    private $prospectiveDriver;
    /** @var AppLocation */
    private $work;

    public function setUp()
    {
        parent::setUp();
        $this->userService->newUser('Chris', 'Holland');
        $this->userService->newUser('Scott', 'Sims');
        $this->userService->newUser('Prospective', 'Driver');
        /** @var AppUser $user */
        $this->userTwo = $this->userService->getUserById(2);
        $this->userOne = $this->userService->getUserById(1);
        $this->prospectiveDriver = $this->userService->getUserById(3);

        $this->home = $this->locationService->getLocation(
            37.773160,
            -122.432444
        );

        $this->work = $this->locationService->getLocation(
            37.7721718,
            -122.4310872
        );

        $this->save(AppRole::asPassenger());
        $this->save(AppRole::asDriver());

        $this->userService->assignRoleToUser($this->prospectiveDriver, AppRole::asDriver());

        $this->save(RideEventType::asRequested());
        $this->save(RideEventType::asAccepted());
        $this->save(RideEventType::inProgress());
        $this->save(RideEventType::asCancelled());
        $this->save(RideEventType::asCompleted());
        $this->save(RideEventType::asRejected());
        $this->save(RideEventType::asDestination());
    }

    public function testCreateAndRetrieveUser()
    {
        self::assertEquals('Scott', $this->userTwo->getFirstName());
        self::assertEquals('Chris', $this->userOne->getFirstName());
    }

    public function testMakeUserPassenger()
    {
        $this->userService->assignRoleToUser($this->userOne, AppRole::asPassenger());
        self::assertTrue($this->userService->isUserPassenger($this->userOne));
    }

    public function testMakeUserDriver()
    {
        $this->userService->assignRoleToUser($this->userOne, AppRole::asDriver());
        self::assertTrue($this->userService->isUserDriver($this->userOne));
    }

    public function testUserHasBothRoles()
    {
        $this->userService->assignRoleToUser($this->userOne, AppRole::asPassenger());
        $this->userService->assignRoleToUser($this->userOne, AppRole::asDriver());
        self::assertTrue($this->userService->isUserPassenger($this->userOne));
        self::assertTrue($this->userService->isUserDriver($this->userOne));
    }

    public function testDuplicateRoleAssignmentThrowsException()
    {
        $this->userService->assignRoleToUser($this->userOne, AppRole::asPassenger());
        self::expectException(RoleLifeCycleException::class);
        $this->userService->assignRoleToUser($this->userOne, AppRole::asPassenger());
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

    public function testAssignDestinationToRide()
    {
        $ride = $this->makePassengerRide();
        $this->rideService->assignDestinationToRide($ride, $this->work);

        $ride = $this->getFirstRideForUserOne();
        self::assertTrue($ride->getDestination()->equals($this->work));
    }

    public function testMarkRideAsRequested()
    {
        $ride = $this->makePassengerRide();
        $this->rideService->passengerMarkRideAs($ride, RideEventType::asRequested());

        $rideStatus = $this->rideService->getRideStatus($ride);
        self::assertEquals("Requested", $rideStatus->getType()->getName());
    }

    public function testCheckRideStatus()
    {
        $ride = $this->makePassengerRide();
        $this->rideService->passengerMarkRideAs($ride, RideEventType::asRequested());

        self::assertTrue($this->rideService->isRide($ride, RideEventType::asRequested()));
    }

    public function testAssignDriverToRide()
    {
        $ride = $this->makePassengerRide();
        $this->rideService->passengerMarkRideAs($ride, RideEventType::asRequested());

        $this->assignDriverToRide($ride);

        self::assertTrue($this->userService->isUserDriver($ride->getDriver()));
    }

    public function testRideEventLifeCycles()
    {
        $ride = $this->makePassengerRide();
        $this->rideService->passengerMarkRideAs($ride, RideEventType::asRequested());
        self::assertTrue($this->rideService->isRide($ride, RideEventType::asRequested()));

        $this->assignDriverToRide($ride);

        $this->rideService->driverMarkRideAs($ride, RideEventType::asAccepted());
        self::assertTrue($this->rideService->isRide($ride, RideEventType::asAccepted()));

        $this->rideService->passengerMarkRideAs($ride, RideEventType::asDestination());
        self::assertTrue($this->rideService->isRide($ride, RideEventType::asDestination()));

        $this->rideService->driverMarkRideAs($ride, RideEventType::inProgress());
        self::assertTrue($this->rideService->isRide($ride, RideEventType::inProgress()));
        
        $this->rideService->driverMarkRideAs($ride, RideEventType::asCompleted());
        self::assertTrue($this->rideService->isRide($ride, RideEventType::asCompleted()));
    }

    public function testMarkRideAsAssignedDestinationFromRequested() {

        $ride = $this->getRequestedPassengerRide();
        $this->rideService->passengerMarkRideAs($ride, RideEventType::asDestination());
        $this->rideService->assignDestinationToRide($ride, $this->work);
        self::assertTrue($this->rideService->isRide($ride, RideEventType::asDestination()));
    }

    public function testMarkRideAsAssignedDestinationFromAccepted()
    {
        $ride = $this->getAcceptedPassengerRide();
        $this->rideService->passengerMarkRideAs($ride, RideEventType::asDestination());
        $this->rideService->assignDestinationToRide($ride, $this->work);
        self::assertTrue($this->rideService->isRide($ride, RideEventType::asDestination()));
    }

    public function testOutOfSequenceDestinationAssignmentThrows()
    {
        $ride = $this->getAcceptedPassengerRide();
        $this->rideService->passengerMarkRideAs($ride, RideEventType::asDestination());
        $this->rideService->driverMarkRideAs($ride, RideEventType::inProgress());
        self::expectException(RideEventLifeCycleException::class);
        $this->rideService->passengerMarkRideAs($ride, RideEventType::asDestination());
    }

    public function testOutOfSequenceRequestedEventThrows()
    {
        $ride = $this->makePassengerRide();
        $this->rideService->passengerMarkRideAs($ride, RideEventType::asRequested());
        self::expectException(RideEventLifeCycleException::class);
        $this->rideService->passengerMarkRideAs($ride, RideEventType::asRequested());
    }

    public function testUnassignedDriverThrows()
    {
        $ride = $this->makePassengerRide();
        self::expectException(UnassignedDriverException::class);
        $this->rideService->driverMarkRideAs($ride, RideEventType::asAccepted());

    }

    public function testOutOfSequenceAcceptedEventThrows()
    {
        $ride = $this->makePassengerRide();
        $this->assignDriverToRide($ride);
        self::expectException(RideEventLifeCycleException::class);
        $this->rideService->driverMarkRideAs($ride, RideEventType::asAccepted());
    }

    public function testOutOfSequenceInProgressEventThrows()
    {
        $ride = $this->makePassengerRide();
        $this->rideService->passengerMarkRideAs($ride, RideEventType::asRequested());
        $this->assignDriverToRide($ride);
        $this->rideService->driverMarkRideAs($ride, RideEventType::asAccepted());
        self::expectException(RideEventLifeCycleException::class);
        $this->rideService->driverMarkRideAs($ride, RideEventType::inProgress());
    }

    public function testOutOfSequenceCancelledEventThrows()
    {
        $ride = $this->getAcceptedPassengerRide();
        $this->rideService->passengerMarkRideAs($ride, RideEventType::asDestination());
        $this->rideService->driverMarkRideAs($ride, RideEventType::inProgress());
        self::expectException(RideEventLifeCycleException::class);
        $this->rideService->driverMarkRideAs($ride, RideEventType::asCancelled());
    }

    public function testCancelledEventWorks()
    {
        $ride = $this->getAcceptedPassengerRide();
        $this->rideService->driverMarkRideAs($ride, RideEventType::asCancelled());
        self::assertTrue($this->rideService->isRide($ride, RideEventType::asCancelled()));
    }

    public function testOutOfSequenceCompletedThrows()
    {
        $ride = $this->getAcceptedPassengerRide();
        self::expectException(RideEventLifeCycleException::class);
        $this->rideService->driverMarkRideAs($ride, RideEventType::asCompleted());
    }

    public function testOutOfSequenceRejectedThrows()
    {
        $ride = $this->getAcceptedPassengerRide();
        self::expectException(RideEventLifeCycleException::class);
        $this->rideService->driverMarkRideAs($ride, RideEventType::asRejected());
    }

    public function testRejectedWorks()
    {
        $ride = $this->makePassengerRide();
        $this->rideService->passengerMarkRideAs($ride, RideEventType::asRequested());
        $this->rideService->prospectiveDriverMarkRideAs($ride, RideEventType::asRejected(), $this->prospectiveDriver);
        self::assertTrue($this->rideService->isRide($ride, RideEventType::asRejected()));
    }

    /**
     * life-cycle:
     *
     * passenger: requestRide
     * driver: accept ride
     * passenger: set destination
     * driver: start ride
     * driver: complete ride
     */

    public function testRequestRideHasProperStateAndAttributes()
    {
        $ride = $this->getRequestedPassengerRide();

        self::assertEquals('Chris', $ride->getPassenger()->getFirstName());
        self::assertTrue($this->rideService->isRide($ride, RideEventType::asRequested()));
    }

    public function testAcceptRideHasProperStateAndAttributes()
    {
        $ride = $this->getAcceptedRequestedPassengerRide();

        self::assertTrue($ride->hasDriver());
        self::assertTrue($this->rideService->isRide($ride, RideEventType::asAccepted()));
    }

    public function testSetDestinationHasProperStateAndAttributes()
    {
        $ride = $this->getDestinationAcceptedRequestedPassengerRide();

        self::assertTrue($ride->getDestination()->equals($this->work));
        self::assertTrue($this->rideService->isRide($ride, RideEventType::asDestination()));
    }

    public function testStartRideHasProperStateAndAttributes()
    {
        $ride = $this->getDestinationAcceptedRequestedPassengerRide();

        $this->rideService->startRide($ride);

        self::assertTrue($this->rideService->isRide($ride, RideEventType::inProgress()));
    }

    public function testCompleteRideHasProperStateAndAttributes()
    {
        $ride = $this->getDestinationAcceptedRequestedPassengerRide();
        $this->rideService->startRide($ride);

        $this->rideService->completeRide($ride);

        self::assertTrue($this->rideService->isRide($ride, RideEventType::asCompleted()));
    }

    /**
     * @return Ride
     */
    private function makePassengerRide()
    {
        $this->makeUserOnePassenger();
        $this->rideService->createRide(
            $this->userOne,
            $this->home
        );

        /** @var Ride[] $ridesForUser */
        $firstRide = $this->getFirstRideForUserOne();

        return $firstRide;
    }

    /**
     * @param $ride
     */
    private function assignDriverToRide($ride)
    {
        $this->userService->assignRoleToUser($this->userTwo, AppRole::asDriver());
        $this->rideService->assignDriverToRide($ride, $this->userTwo);
    }

    /**
     * @return Ride
     */
    private function getAcceptedPassengerRide()
    {
        $ride = $this->makePassengerRide();
        $this->rideService->passengerMarkRideAs($ride, RideEventType::asRequested());
        $this->assignDriverToRide($ride);
        $this->rideService->driverMarkRideAs($ride, RideEventType::asAccepted());

        return $ride;
    }

    private function makeUserOnePassenger()
    {
        $this->userService->assignRoleToUser($this->userOne, AppRole::asPassenger());
    }

    /**
     * @return Ride
     */
    private function getFirstRideForUserOne()
    {
        $ridesForUser = $this->rideService->getRidesForPassenger($this->userOne);
        self::assertCount(1, $ridesForUser);
        $firstRide = $ridesForUser[0];

        return $firstRide;
    }

    /**
     * @return Ride
     */
    private function getRequestedPassengerRide()
    {
        $this->makeUserOnePassenger();
        $this->rideService->requestRide($this->userOne, $this->home);
        $firstRide = $this->getFirstRideForUserOne();

        return $firstRide;
    }

    /**
     * @return Ride
     */
    private function getAcceptedRequestedPassengerRide()
    {
        $ride = $this->getRequestedPassengerRide();
        $this->userService->assignRoleToUser($this->userTwo, AppRole::asDriver());
        $this->rideService->driverAcceptRide($ride, $this->userTwo);

        $ride = $this->getFirstRideForUserOne();

        return $ride;
    }

    /**
     * @return Ride
     */
    private function getDestinationAcceptedRequestedPassengerRide()
    {
        $ride = $this->getAcceptedRequestedPassengerRide();
        $this->rideService->setDestinationForRide($ride, $this->work);

        $ride = $this->getFirstRideForUserOne();

        return $ride;
    }
}
