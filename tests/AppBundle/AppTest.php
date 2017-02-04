<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEventType;
use AppBundle\RoleLifeCycleException;

/**
 * Consult ride-hailing.svg to digest some key application concepts.
 *
 * Consult Kata-Tasks.rtf to get an idea of the various tests you'll be writing
 * and help shape your sequencing.
 *
 * With this said, you do not have to follow the sequencing outlined.
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
        $this->appService->markRideAsRequested($ride);

        $rideStatus = $this->appService->getRideStatus($ride);
        self::assertEquals("Requested", $rideStatus->getType()->getName());
    }

    public function testCheckRideStatus()
    {
        $ride = $this->makePassengerRide();
        $this->appService->markRideAsRequested($ride);

        self::assertTrue($this->appService->isRide($ride, RideEventType::asRequested()));
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
}
