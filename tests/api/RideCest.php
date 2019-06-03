<?php

namespace Tests\api;

use ApiTester;
use Tests\AppBundle\Location\LocationRepositoryTest;

class RideCest
{
    public function testRideCreation(ApiTester $I)
    {

        $passengerUserId = $I->createPassengerAndGetId();
        $driverUserId = $I->createDriverAndGetId();

        $createdRide = $I->sendPostApiRequest(
            '/ride',
            [
                'passenger_id' => $passengerUserId,
                'departure_lat' => LocationRepositoryTest::HOME_LOCATION_LAT,
                'departure_long' => LocationRepositoryTest::HOME_LOCATION_LONG
            ]
        );

        $I->seeResponseContainsJson(
            [
                'passenger_id' => $passengerUserId,
                'departure_lat' => LocationRepositoryTest::HOME_LOCATION_LAT,
                'departure_long' => LocationRepositoryTest::HOME_LOCATION_LONG
            ]
        );

        $rideId = $createdRide['id'];

        $I->sendPatchApiRequest(
            '/ride/'.$rideId,
            [
                'driver_id' => $driverUserId
            ]
        );

        $I->seeResponseContainsJson(
            [
                'passenger_id' => $passengerUserId,
                'departure_lat' => LocationRepositoryTest::HOME_LOCATION_LAT,
                'departure_long' => LocationRepositoryTest::HOME_LOCATION_LONG,
                'driver_id' => $driverUserId
            ]
        );

        $I->sendPatchApiRequest(
            '/ride/'.$rideId,
            [
                'destination_lat' => LocationRepositoryTest::WORK_LOCATION_LAT,
                'destination_long' => LocationRepositoryTest::WORK_LOCATION_LONG
            ]
        );

        $I->seeResponseContainsJson(
            [
                'passenger_id' => $passengerUserId,
                'departure_lat' => LocationRepositoryTest::HOME_LOCATION_LAT,
                'departure_long' => LocationRepositoryTest::HOME_LOCATION_LONG,
                'driver_id' => $driverUserId,
                'destination_lat' => LocationRepositoryTest::WORK_LOCATION_LAT,
                'destination_long' => LocationRepositoryTest::WORK_LOCATION_LONG
            ]
        );
    }
}
