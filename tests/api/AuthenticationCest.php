<?php
namespace Tests\api;

use ApiTester;

class AuthenticationCest
{
    /**
     * @param ApiTester $I
     */
    public function seeNewUserBadlyAuthenticatedUnAuthorized(ApiTester $I)
    {
        $newUser = $I->getRegisteredUserWithToken('Joe', 'Passenger');
        $userId = $newUser['user']['id'];
        $I->nukeToken();
        $response = $I->sendGetApiRequest('/user/' . $userId);
        $I->seeResponseContainsJson([
            'error' => 'access_denied',
            'error_description' => 'OAuth2 authentication required'
        ]);
    }
}
