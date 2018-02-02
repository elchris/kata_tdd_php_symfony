<?php


namespace Tests\AppBundle\Entity;

use AppBundle\Entity\AppRole;
use Tests\AppBundle\AppTestCase;

class AppRoleTest extends AppTestCase
{
    public function testRoleMatching()
    {
        self::assertTrue(AppRole::isPassenger(AppRole::PASSENGER));
        self::assertTrue(AppRole::isDriver(AppRole::DRIVER));
        self::assertFalse(AppRole::isPassenger(AppRole::DRIVER));
        self::assertFalse(AppRole::isDriver(AppRole::PASSENGER));
    }
}
