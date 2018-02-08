<?php

namespace Tests\AppBundle\DTO;

use AppBundle\DTO\RideDto;
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
        $passenger = new AppUser('Joe', 'Passenger');
        $passenger->assignRole(AppRole::passenger());
        $home = new AppLocation(
            LocationApi::HOME_LOCATION_LAT,
            LocationApi::HOME_LOCATION_LONG
        );
        $ride = new Ride(
            $passenger,
            $home
        );
        $rideDto = new RideDto($ride);

        self::assertSame($passenger->getId()->toString(), $rideDto->passengerId);
        self::assertNull($rideDto->driverId);
        self::assertNull($rideDto->destination);
    }

    public function testRideDtoWithDriverAndDestination()
    {
        $passenger = new AppUser('Joe', 'Passenger');
        $passenger->assignRole(AppRole::passenger());
        $driver = new AppUser('Bob', 'Driver');
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
        $rideDto = new RideDto($ride);

        self::assertSame($driver->getId()->toString(), $rideDto->driverId);
        self::assertTrue($work->isSameAs($rideDto->destination));
    }
}
