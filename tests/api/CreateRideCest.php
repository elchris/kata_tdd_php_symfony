<?php
namespace Tests\api;

use ApiTester;

class CreateRideCest
{
    public function seeNewRideIsRequested(ApiTester $I)
    {
        $I->getNewRide();
    }
}
