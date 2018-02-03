<?php
namespace Tests\api;

use ApiTester;

class MarkRideAcceptedByDriverCest
{
    /**
     * @depends Tests\api\CreateRideCest:seeNewlyCreatedRideIsRequested
     * @param ApiTester $I
     */
    public function seeRideAcceptedByDriver(ApiTester $I)
    {
        $requestedRide = $I->getNewRide();
        $driver = $I->getNewDriver();

        $driverId = $driver['id'];
        $rideId = $requestedRide['id'];
        $I->acceptRideByDriver(
            $rideId,
            $driverId,
            $requestedRide['passenger']['id']
        );
        $I->assignWorkDestinationToRide($rideId);
    }
}
