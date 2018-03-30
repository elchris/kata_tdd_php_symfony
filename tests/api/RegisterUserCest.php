<?php
namespace Tests\api;

use ApiTester;
use Codeception\Util\HttpCode;
use PHPUnit\Framework\TestResult;
use Tests\AppBundle\Production\UserApi;

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
                'first' => 'Dan',
                'last' => 'Fritcher'
            ]
        );

        $I->canSeeResponseContainsJson(
            [
                'first' => 'Dan',
                'last' => 'Fritcher'
            ]
        );

        print_r($response);
    }
}
