<?php

use AppBundle\Entity\AppRole;
use AppBundle\Entity\RideEventType;
use Codeception\Util\HttpCode;
use Tests\AppBundle\Production\LocationApi;
use Tests\AppBundle\Production\UserApi;
use Tests\AppBundle\User\FakeUser;

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

    public function markRideInProgress($rideId, $driverId)
    {
        $this->sendPatchApiRequest('/ride/'.$rideId, [
            'driverId' => $driverId,
            'eventId' => RideEventType::IN_PROGRESS_ID
        ]);
        $this->verifyDriver($driverId);
        $this->verifyRideStatus(
            $rideId,
            RideEventType::IN_PROGRESS_ID,
            RideEventType::IN_PROGRESS_STATUS
        );
    }

    public function markRideCompleted($rideId, $driverId)
    {
        $this->sendPatchApiRequest('/ride/'.$rideId, [
            'driverId' => $driverId,
            'eventId' => RideEventType::COMPLETED_ID
        ]);
        $this->verifyDriver($driverId);
        $this->verifyRideStatus(
            $rideId,
            RideEventType::COMPLETED_ID,
            RideEventType::COMPLETED
        );
    }
    
    public function acceptRideByDriver(string $rideId, string $driverId)
    {
        $patchedRide = $this->sendPatchApiRequest('/ride/'. $rideId, [
            'driverId' => $driverId,
            'eventId' => RideEventType::ACCEPTED_ID
        ]);
        $this->verifyDriver($driverId);
        $this->verifyRideStatus(
            $rideId,
            RideEventType::ACCEPTED_ID,
            RideEventType::ACCEPTED
        );
        return $patchedRide;
    }

    public function assignDestinationToRide(
        string $rideId,
        $destinationLat,
        $destinationLong
    ) {
        $patchedRide = $this->sendPatchApiRequest('/ride/'.$rideId, [
            'destinationLat' => $destinationLat,
            'destinationLong' => $destinationLong
        ]);
        $this->seeResponseContainsJson([
            'destination' => [
                'lat' => $destinationLat,
                'long' => $destinationLong
            ]
        ]);
        return $patchedRide;
    }

    public function assignWorkDestinationToRide(string $rideId)
    {
        $destinationLat = LocationApi::WORK_LOCATION_LAT;
        $destinationLong = LocationApi::WORK_LOCATION_LONG;
        $this->assignDestinationToRide($rideId, $destinationLat, $destinationLong);
    }

    public function getNewRide()
    {
        $passenger = $this->getNewPassenger();

        $createdRide = $this->sendPostApiRequest('/ride', [
            'passengerId' => $passenger['id'],
            'departureLat' => LocationApi::HOME_LOCATION_LAT,
            'departureLong' => LocationApi::HOME_LOCATION_LONG
        ]);
        $rideId = $createdRide['id'];

        $this->sendGetApiRequest('/ride/'.$rideId);
        $this->seeResponseContainsJson([
            'id' => $rideId
        ]);

        $this->verifyPassenger($passenger['id']);

        $statusIdToVerify = RideEventType::REQUESTED_ID;
        $statusNameToVerify = RideEventType::REQUESTED;
        $this->verifyRideStatus($rideId, $statusIdToVerify, $statusNameToVerify);
        return $createdRide;
    }

    public function getNewPassenger()
    {
        $newUser = $this->getRegisteredUserWithToken('Joe', 'Passenger');
        $userId = $newUser['user']['id'];

        $retrievedUser = $this->retrieveUser($userId);
        $userId = $retrievedUser['id'];

        $patchedUser = $this->assignPassengerRoleToUser($userId);
        $this->verifyPassengerRoleOnUser();
        return $patchedUser;
    }

    public function getNewDriver()
    {
        $newUser = $this->createNewUser('Bob', 'Driver');
        $userId = $newUser['id'];

        $this->loginWithUserName($newUser['username']);
        $retrievedUser = $this->retrieveUser($userId);
        $userId = $retrievedUser['id'];

        $patchedUser = $this->assignDriverRoleToUser($userId);
        $this->verifyDriverRoleOnUser();
        return $patchedUser;
    }

    protected function verifyPassengerRoleOnUser()
    {
        $this->seeResponseContainsJson(
            [
                'is_passenger' => true
            ]
        );
    }

    protected function verifyDriverRoleOnUser()
    {
        $this->seeResponseContainsJson(
            [
                'is_driver' => true
            ]
        );
    }

    /**
     * @param $userId
     * @return mixed
     */
    protected function assignPassengerRoleToUser($userId)
    {
        return $this->assignRoleToUser($userId, AppRole::PASSENGER);
    }

    /**
     * @param $userId
     * @return mixed
     */
    protected function assignDriverRoleToUser($userId)
    {
        return $this->assignRoleToUser($userId, AppRole::DRIVER);
    }

    protected function createNewUser(string $first, string $last)
    {
        $fakedUser = new FakeUser($first, $last);
        $response = $this->sendPostApiRequest('/register-user', [
            'firstName' => $first,
            'lastName' => $last,
            'email' => $fakedUser->email,
            'username' => $fakedUser->username,
            'password' => $fakedUser->password
        ]);
        return $response;
    }

    private $token;

    public function getRegisteredUserWithToken($first, $last, $skipLogin = false)
    {
        $createdUser = $this->createNewUser($first, $last);
        $username = $createdUser['username'];
        if (! $skipLogin) {
            $response = $this->loginWithUserName($username);
        }
        $response['user'] = $createdUser;
        return $response;
    }

    /**
     * @param $userId
     * @return mixed
     */
    public function retrieveUser($userId)
    {
        $response = $this->sendGetApiRequest('/user/' . $userId);
        $this->seeResponseContainsJson(['id' => $userId]);

        return $response;
    }

    /**
     * @param $userId
     * @param $roleToAssign
     * @return mixed
     */
    private function assignRoleToUser($userId, $roleToAssign)
    {
        $patchedUser = $this->sendPatchApiRequest(
            '/user/' . $userId,
            [
                'role' => $roleToAssign
            ]
        );

        return $patchedUser;
    }

    /**
     * @param $rideId
     * @param $statusIdToVerify
     * @param $statusNameToVerify
     */
    public function verifyRideStatus($rideId, $statusIdToVerify, $statusNameToVerify): void
    {
        $this->sendGetApiRequest('/ride/' . $rideId . '/status');
        $this->seeResponseContainsJson([
            'id' => $statusIdToVerify,
            'name' => $statusNameToVerify
        ]);
    }

    /**
     * @param $driverId
     */
    private function verifyDriver($driverId): void
    {
        $this->seeResponseContainsJson([
            'driver_id' => $driverId
        ]);
    }

    /**
     * @param $passengerId
     */
    private function verifyPassenger($passengerId): void
    {
        $this->seeResponseContainsJson([
            'passenger_id' => $passengerId
        ]);
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
}
