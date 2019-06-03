<?php

namespace Tests\api;

use ApiTester;
use Tests\AppBundle\Location\LocationRepositoryTest;

class RideCest
{
    public function testRideCreation(ApiTester $I)
    {

        $passengerUserId = $I->createPassengerAndGetId();
        $I->sendPostApiRequest(
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
    }
}
