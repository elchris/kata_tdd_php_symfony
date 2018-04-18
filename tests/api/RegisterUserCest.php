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
        $dan = [
            'first' => 'dan',
            'last' => 'fritcher'
        ];

        $response = $I->sendPostApiRequest(
            '/register-user',
            $dan
        );

        $I->canSeeResponseContainsJson(
            $dan
        );

        print_r($response);

        $userId = $response['id'];

        $I->sendGetApiRequest(
            '/user/'.$userId
        );

        $I->canSeeResponseContainsJson(
            $dan
        );
    }
}
