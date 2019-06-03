<?php
namespace Tests\api;

use ApiTester;

class RegisterUserCest
{
    /**
     * @param ApiTester $I
     */
    public function seeNewUserCreated(ApiTester $I)
    {
        $createdUser = $I->sendPostApiRequest(
            '/user',
            [
                'first' => 'chris',
                'last' => 'holland'
            ]
        );

        $I->seeResponseContainsJson(
            [
                'first' => 'chris',
                'last' => 'holland'
            ]
        );

        $userId = $createdUser['id'];
    }
}
