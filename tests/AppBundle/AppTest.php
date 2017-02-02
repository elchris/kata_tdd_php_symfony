<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;

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
    }

    public function testCreateAndRetrieveUser()
    {
        self::assertEquals('Scott', $this->userTwo->getFirstName());
        self::assertEquals('Chris', $this->userOne->getFirstName());
    }

    public function testMakeUserPassenger()
    {
        $this->save(AppRole::asPassenger());
        $this->appService->assignRoleToUser($this->userOne, AppRole::asPassenger());
        self::assertTrue($this->appService->isUserPassenger($this->userOne));
    }

    public function testMakerUserDriver()
    {
        $this->save(AppRole::asDriver());
        $this->appService->assignRoleToUser($this->userOne, AppRole::asDriver());
        self::assertTrue($this->appService->isUserDriver($this->userOne));
    }
}
