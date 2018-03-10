<?php
namespace Tests\api;

use ApiTester;
use Codeception\Util\HttpCode;
use PHPUnit\Framework\TestResult;
use Tests\AppBundle\Production\UserApi;

class AuthenticationCest
{
    /**
     * @param ApiTester $I
     */
    public function seeNewUserBadlyAuthenticatedUnAuthorized(ApiTester $I)
    {
        $newUser = $I->getRegisteredUserWithToken(
            'Joe',
            'Passenger',
            true
        );
        $userName = $newUser['user']['username'];
        $userId = $newUser['user']['id'];
        $I->nukeToken();
        $response = $I->sendGetApiRequest('/user/' . $userId);
        $I->seeResponseContainsJson([
            'error' => 'access_denied',
            'error_description' => 'OAuth2 authentication required'
        ]);
    }
}
