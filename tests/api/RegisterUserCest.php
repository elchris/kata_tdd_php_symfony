<?php
namespace Tests\api;

use ApiTester;
use AppBundle\Entity\AppRole;

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
                'first' => 'chris',
                'last' => 'holland'
            ]
        );

        $I->canSeeResponseContainsJson(
            [
                'first' => 'chris',
                'last' => 'holland'
            ]
        );

        $userId = $response['id']; //from UserDto

        $I->sendPatchApiRequest(
            '/user/'.$userId,
            [
                'role' => AppRole::PASSENGER
            ]
        );

        $I->canSeeResponseContainsJson(
            [
                'roles' => [
                    0 => [
                        'id' => 1,
                        'name' => AppRole::PASSENGER
                    ]
                ]
            ]
        );

        $response = $I->sendPatchApiRequest(
            '/user/'.$userId,
            [
                'role' => AppRole::DRIVER
            ]
        );

        $I->canSeeResponseContainsJson(
            [
                'roles' => [
                    0 => [
                        'id' => 1,
                        'name' => AppRole::PASSENGER
                    ],
                    1 => [
                        'id' => 2,
                        'name' => AppRole::DRIVER
                    ]
                ]
            ]
        );

        print_r($response);
    }
}
