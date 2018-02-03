<?php
namespace Tests\api;

use ApiTester;

class CreateRideCest
{
    /**
     * @depends Tests\api\CreatePassengerCest:seeNewPassengerCreated
     * @param ApiTester $I
     */
    public function seeNewlyCreatedRideIsRequested(ApiTester $I)
    {
        $I->getNewRide();
    }
}
