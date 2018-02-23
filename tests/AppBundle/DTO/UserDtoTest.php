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
            ($this->newNamedUser(
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
        $driver = $this->newNamedUser('Joe', 'Driver');
        self::assertTrue($driver->isNamed('Joe Driver'));
        $newFirstName = 'NotJoe';
        $newLastName = 'NotDriver';
        $driver->setFirstName($newFirstName);
        $driver->setLastName($newLastName);
        self::assertSame($newFirstName, $driver->getFirstName());
        self::assertSame($newLastName, $driver->getLastName());
        $driver->assignRole(AppRole::driver());
        $userDto = $driver->toDto();
        self::assertTrue($userDto->isDriver);
        self::assertFalse($userDto->isPassenger);
        self::assertSame($driver->getUsername(), $userDto->username);
    }

    public function testUserDtoPassenger()
    {
        $passenger = $this->newNamedUser('Bob', 'Passenger');
        $passenger->assignRole(AppRole::passenger());
        $userDto = $passenger->toDto();
        self::assertTrue($userDto->isPassenger);
        self::assertFalse($userDto->isDriver);
    }
}
