<?php

namespace Tests\AppBundle\DTO;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use Tests\AppBundle\AppTestCase;

class UserDtoTest extends AppTestCase
{
    public function testUserDtoWithNoRoles()
    {
        $userDto =
            (new AppUser(
                'chris',
                'holland'
            ))->toDto();


        self::assertNotNull($userDto->id);
        self::assertFalse($userDto->isDriver);
        self::assertFalse($userDto->isPassenger);
        self::assertEquals('chris holland', $userDto->fullName);
    }

    public function testUserDtoDriver()
    {
        $driver = new AppUser('Joe', 'Driver');
        $driver->assignRole(AppRole::driver());
        $userDto = $driver->toDto();
        self::assertTrue($userDto->isDriver);
        self::assertFalse($userDto->isPassenger);
    }

    public function testUserDtoPassenger()
    {
        $passenger = new AppUser('Bob', 'Passenger');
        $passenger->assignRole(AppRole::passenger());
        $userDto = $passenger->toDto();
        self::assertTrue($userDto->isPassenger);
        self::assertFalse($userDto->isDriver);
    }
}
