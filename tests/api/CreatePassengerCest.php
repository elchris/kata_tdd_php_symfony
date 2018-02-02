<?php
namespace Tests\api;

use ApiTester;

class CreatePassengerCest
{
    public function getNewPassenger(ApiTester $I)
    {
        $I->getNewPassenger();
    }
}
