<?php
namespace Tests\api;

use ApiTester;
use AppBundle\Entity\AppRole;

class RegisterUserCest
{
    /**
     * @param ApiTester $I
     */
    public function seeNewUserCreated(ApiTester $I): void
    {
        $response = $I->sendPostApiRequest(
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

        $userId = $response['id'];

        $I->sendPatchApiRequest(
            '/user/'.$userId,
            [
                'role' => AppRole::PASSENGER
            ]
        );

        $I->seeResponseContainsJson(
            [
                'first' => 'chris',
                'last' => 'holland',
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

        $I->seeResponseContainsJson(
            [
                'first' => 'chris',
                'last' => 'holland',
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
}
