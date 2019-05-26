<?php

namespace Tests\api;
use ApiTester;
use AppBundle\Repository\LocationRepository;
use Tests\AppBundle\Location\LocationRepositoryTest;

class CreateRideCest
{
    public function seeNewRideCreated(ApiTester $I)
    {
        $createdPassengerId = $I->getNewPassenger();

        $createdRide = $I->sendPostApiRequest(
            '/ride',
            [
                'passengerId' => $createdPassengerId,
                'departureLat' => LocationRepositoryTest::HOME_LOCATION_LAT,
                'departureLong' => LocationRepositoryTest::HOME_LOCATION_LONG
            ]
        );

        $I->seeResponseContainsJson(
            [
                'passengerId' => $createdPassengerId,
                'departureLat' => LocationRepositoryTest::HOME_LOCATION_LAT,
                'departureLong' => LocationRepositoryTest::HOME_LOCATION_LONG
            ]
        );
    }
}
