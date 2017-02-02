<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
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

        $this->save(AppRole::asPassenger());
        $this->save(AppRole::asDriver());
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
        $home = $this->appService->getLocation(
            37.773160,
            -122.432444
        );
        self::assertEquals(37.773160, $home->getLat(), 0.00000001);
        self::assertEquals(-122.432444, $home->getLong(), 0.00000001);
    }
}
