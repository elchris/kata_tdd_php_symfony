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

        $userId = $response['id'];

        $I->sendGetApiRequest(
            '/user/'.$userId
        );

        $I->canSeeResponseContainsJson(
            $dan
        );

        $response = $I->sendPatchApiRequest(
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
