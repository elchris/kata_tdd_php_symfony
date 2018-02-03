<?php
namespace Tests\api;

use ApiTester;

class CreatePassengerCest
{
    /**
     * @param ApiTester $I
     */
    public function seeNewPassengerCreated(ApiTester $I)
    {
        $I->getNewPassenger();
    }
}
