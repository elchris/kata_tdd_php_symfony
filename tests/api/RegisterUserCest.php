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

        $patchedUser = $I->sendPatchApiRequest(
            '/user/'.$userId,
            [
                'role' => 'Passenger'
            ]
        );

        $I->canSeeResponseContainsJson(
            [
                'roles' => [
                    0 => [
                        'id' => 1,
                        'name' => 'Passenger'
                    ]
                ]
            ]
        );
    }

//,
//1 => [
//'id' => 2,
//'name' => 'Driver'
//]
}
