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
        $response = $I->sendPostApiRequest(
            '/register-user',
            [
                'firstName' => 'chris',
                'lastName' => 'holland'
            ]
        );

        $I->canSeeResponseContainsJson(
            [
                'firstName' => 'chris',
                'lastName' => 'holland'
            ]
        );

        $userId = $response['id'];

        $I->sendPatchApiRequest(
            '/user/'.$userId,
            [
                'role' => 'Passenger'//AppRole::PASSENGER_NAME
            ]
        );
        $I->canSeeResponseContainsJson(
            [
                'roles' => [
                    0 => [
                        'id' => 1,
                        'name' => 'Passenger'//AppRole::PASSENGER_NAME
                    ]
                ]
            ]
        );
        $I->sendPatchApiRequest(
            '/user/'.$userId,
            [
                'role' => 'Driver'//AppRole::DRIVER_NAME
            ]
        );
        $I->canSeeResponseContainsJson(
            [
                'roles' => [
                    0 => [
                        'id' => 1,
                        'name' => 'Passenger'//AppRole::PASSENGER_NAME
                    ],
                    1 => [
                        'id' => 2,
                        'name' => 'Driver'//AppRole::DRIVER_NAME
                    ]
                ]
            ]
        );
    }
}
