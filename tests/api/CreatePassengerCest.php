<?php
namespace Tests\api;

use ApiTester;

class CreatePassengerCest
{
    /**
     * @depends Tests\api\AuthenticationCest:seeNewUserBadlyAuthenticatedUnAuthorized
     * @param ApiTester $I
     */
    public function seeNewPassengerCreated(ApiTester $I)
    {
        $I->getNewPassenger();
    }
}
