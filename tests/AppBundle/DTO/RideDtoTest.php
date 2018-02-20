<?php

namespace Tests\AppBundle\DTO;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Production\LocationApi;

class RideDtoTest extends AppTestCase
{
    public function testRideDtoNoDriverNoDestination()
    {
        $firstName = 'Joe';
        $lastName = 'Passenger';
        $passenger = $this->newNamedUser($firstName, $lastName);
        $passenger->assignRole(AppRole::passenger());
        $home = new AppLocation(
            LocationApi::HOME_LOCATION_LAT,
            LocationApi::HOME_LOCATION_LONG
        );
        $ride = new Ride(
            $passenger,
            $home
        );
        $rideDto = $ride->toDto();

        self::assertSame($passenger->getId()->toString(), $rideDto->passengerId);
        self::assertNull($rideDto->driverId);
        self::assertNull($rideDto->destination);
    }

    public function testRideDtoWithDriverAndDestination()
    {
        $passenger = $this->newNamedUser('Joe', 'Passenger');
        $passenger->assignRole(AppRole::passenger());
        $driver = $this->newNamedUser('Bob', 'Driver');
        $driver->assignRole(AppRole::driver());
        $home = new AppLocation(
            LocationApi::HOME_LOCATION_LAT,
            LocationApi::HOME_LOCATION_LONG
        );
        $work = new AppLocation(
            LocationApi::WORK_LOCATION_LAT,
            LocationApi::WORK_LOCATION_LONG
        );
        $ride = new Ride(
            $passenger,
            $home
        );
        $ride->assignDriver($driver);
        $ride->assignDestination($work);
        $rideDto = $ride->toDto();

        self::assertSame($driver->getId()->toString(), $rideDto->driverId);
        self::assertTrue($work->isSameAs($rideDto->destination));
    }
}
