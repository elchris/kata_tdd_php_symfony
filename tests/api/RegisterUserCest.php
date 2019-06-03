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
        $userId = $I->createPassengerAndGetId();

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
}
