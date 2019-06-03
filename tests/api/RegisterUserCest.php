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

        $I->sendPatchApiRequest(
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
    }

//,
//1 => [
//'id' => 2,
//'name' => 'Driver'
//]
}
