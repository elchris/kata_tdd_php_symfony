<?php

namespace Tests\AppBundle\DTO;

use AppBundle\DTO\UserDto;
use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use Tests\AppBundle\AppTestCase;

class UserDtoTest extends AppTestCase
{
    public function testUserDtoWithNoRoles()
    {
        $userDto = new UserDto(
            new AppUser('chris', 'holland')
        );

        self::assertNotNull($userDto->id);
        self::assertFalse($userDto->isDriver);
        self::assertFalse($userDto->isPassenger);
        self::assertEquals('chris holland', $userDto->fullName);
    }

    public function testUserDtoDriver()
    {
        $driver = new AppUser('Joe', 'Driver');
        $driver->assignRole(AppRole::driver());
        $userDto = new UserDto($driver);
        self::assertTrue($userDto->isDriver);
        self::assertFalse($userDto->isPassenger);
    }

    public function testUserDtoPassenger()
    {
        $passenger = new AppUser('Bob', 'Passenger');
        $passenger->assignRole(AppRole::passenger());
        $userDto = new UserDto($passenger);
        self::assertTrue($userDto->isPassenger);
        self::assertFalse($userDto->isDriver);
    }
}
