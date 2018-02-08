<?php
namespace Tests\api;

use ApiTester;

class MarkRideInProgressCest
{
    /**
     * @depends Tests\api\MarkRideAcceptedByDriverCest:seeRideAcceptedByDriver
     * @param ApiTester $I
     */
    public function seeRideMarkedInProgress(ApiTester $I)
    {
        $requestedRide = $I->getNewRide();
        $driver = $I->getNewDriver();

        $driverId = $driver['id'];
        $rideId = $requestedRide['id'];

        $I->acceptRideByDriver(
            $rideId,
            $driverId
        );
        $I->assignWorkDestinationToRide($rideId);
        $I->markRideInProgress($rideId, $driverId);
    }
}
