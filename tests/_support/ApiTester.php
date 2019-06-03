<?php

use AppBundle\Entity\AppRole;
use Codeception\Util\HttpCode;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class ApiTester extends \Codeception\Actor
{
    use _generated\ApiTesterActions;

    private $token = null;

    public function sendPostApiRequest(
        $uri,
        $params
    ) {
        $this->haveHttpHeader(
            'Content-Type',
            'application/x-www-form-urlencoded'
        );
        $this->injectToken();
        $this->sendPOST($uri, $params);

        return $this->validateAndReturnResponse();
    }

    public function sendGetApiRequest($uri, $params = [])
    {
        $this->injectToken();
        $this->sendGET($uri, $params);
        return $this->validateAndReturnResponse();
    }

    public function sendPatchApiRequest($uri, $params)
    {
        $this->injectToken();
        $this->sendPATCH($uri, $params);
        return $this->validateAndReturnResponse();
    }

    /**
     * @return mixed
     */
    protected function validateAndReturnResponse()
    {
        if ($this->expectAuthError) {
            $this->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        } else {
            $this->seeResponseCodeIs(HttpCode::OK);
        }
        $this->seeResponseIsJson();

        return json_decode($this->grabResponse(), true);
    }

    private $expectAuthError = false;

    public function nukeToken()
    {
        $this->token = null;
        $this->expectAuthError = true;
    }

    private function injectToken(): void
    {
        if (!is_null($this->token)) {
            $this->amBearerAuthenticated($this->token);
        }
    }

    /**
     * @param $username
     * @return array
     */
    protected function loginWithUserName($username) : array
    {

        $this->amOnPage('/logout');
        $authUrl =
            '/oauth/v2/auth?client_id='
            .UserApi::CLIENT_ID
            .'&redirect_uri=http://localhost:8000/&response_type=token';

        $this->amOnPage($authUrl);
        $this->canSeeInFormFields('form', [
            '_username' => '',
            '_password' => ''
        ]);
        $this->submitForm('form', [
            '_username' => $username,
            '_password' => 'password'
        ], '_submit');
        $this->seeResponseCodeIs(HttpCode::OK);
        $this->submitForm('form[name=fos_oauth_server_authorize_form]', [], 'accepted');
        $token = $this->grabFromCurrentUrl('~.*&access_token=([^&]+)~');
        $this->token = $token;
        return [
            'token_type' => 'bearer',
            'access_token' => $token
        ];
    }


    public function createPassengerAndGetId()
    {
        $firstName = 'chris';
        $lastName = 'holland';
        $roleToAssign = AppRole::PASSENGER;

        return $this->createUserWithRoleAndGetId($firstName, $lastName, $roleToAssign);
    }

    public function createDriverAndGetId()
    {
        $firstName = 'driver';
        $lastName = 'dude';
        $roleToAssign = AppRole::DRIVER;

        return $this->createUserWithRoleAndGetId($firstName, $lastName, $roleToAssign);
    }

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $roleToAssign
     * @return mixed
     */
    private function createUserWithRoleAndGetId(string $firstName, string $lastName, string $roleToAssign)
    {
        $createdUser = $this->sendPostApiRequest(
            '/user',
            [
                'first' => $firstName,
                'last' => $lastName
            ]
        );

        $this->seeResponseContainsJson(
            [
                'first' => $firstName,
                'last' => $lastName
            ]
        );

        $userId = $createdUser['id'];

        $this->sendPatchApiRequest(
            '/user/' . $userId,
            [
                'role' => $roleToAssign
            ]
        );

        return $userId;
    }
}
