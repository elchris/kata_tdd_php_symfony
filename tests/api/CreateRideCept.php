<?php

if (!empty($scenario)) {
    $I = new ApiTester($scenario);
    $passenger = $I->getNewPassenger();
    $driver = $I->getNewDriver();

    $response = $I->sendPostApiRequest('/ride', [
        'passengerId' => $passenger['id'],
        'departureLat' => \Tests\AppBundle\LocationServiceTest::HOME_LOCATION_LAT,
        'departureLong' => \Tests\AppBundle\LocationServiceTest::HOME_LOCATION_LONG
    ]);
    $rideId = $response['id'];
}
