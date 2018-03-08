<?php
namespace Tests\api;

use ApiTester;
use Codeception\Util\HttpCode;

class AuthenticationCest
{
    /**
     * @param ApiTester $I
     */
    public function seeNewUserBadlyAuthenticatedUnAuthorized(ApiTester $I)
    {
        $newUser = $I->getRegisteredUserWithToken('Joe', 'Passenger');
        $userName = $newUser['user']['username'];
        $userId = $newUser['user']['id'];
        $I->nukeToken();
        $response = $I->sendGetApiRequest('/user/' . $userId);
        $I->seeResponseContainsJson([
            'error' => 'access_denied',
            'error_description' => 'OAuth2 authentication required'
        ]);
//        $I->sendPOST('../../login_check', [
//            'username' => $userName,
//            'password' => 'password'
//        ]);
//        $I->canSeeResponseCodeIs(HttpCode::OK);
    }
}
