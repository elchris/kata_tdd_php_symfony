<?php
namespace Tests\api;

use ApiTester;
use AppBundle\Entity\RideEventType;
use Tests\AppBundle\LocationServiceTest;

class MarkRideInProgressCest
{
    public function seeAcceptedRideByDriver(ApiTester $I)
    {
        $requestedRide = $I->getNewRide();
        $driver = $I->getNewDriver();

        $driverId = $driver['id'];
        $rideId = $requestedRide['id'];
        $passengerId = $requestedRide['passenger']['id'];

        $I->acceptRideByDriver(
            $rideId,
            $driverId,
            $passengerId
        );
        $I->assignWorkDestinationToRide($rideId);
        $I->markRideInProgress($rideId, $driverId);
    }
}
