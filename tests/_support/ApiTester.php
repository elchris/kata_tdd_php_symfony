<?php

use AppBundle\Entity\RideEventType;
use Tests\AppBundle\LocationServiceTest;

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
        $this->sendPOST($uri, $params);

        return $this->validateAndReturnResponse();
    }

    public function sendGetApiRequest($uri, $params = [])
    {
        $this->sendGET($uri, $params);
        return $this->validateAndReturnResponse();
    }

    public function sendPatchApiRequest($uri, $params)
    {
        $this->sendPATCH($uri, $params);
        return $this->validateAndReturnResponse();
    }

    /**
     * @return mixed
     */
    protected function validateAndReturnResponse()
    {
        $this->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $this->seeResponseIsJson();

        return json_decode($this->grabResponse(), true);
    }

    public function markRideInProgress($rideId, $driverId)
    {
        $this->sendPatchApiRequest('/ride/'.$rideId, [
            'driverId' => $driverId,
            'eventId' => RideEventType::IN_PROGRESS_ID
        ]);
        $this->seeResponseContainsJson([
            'driver' => [
                'id' => $driverId
            ]
        ]);
        $this->verifyRideStatus(
            $rideId,
            RideEventType::IN_PROGRESS_ID,
            RideEventType::IN_PROGRESS_STATUS
        );
    }
    
    public function acceptRideByDriver(string $rideId, string $driverId, string $passengerId)
    {
        $patchedRide = $this->sendPatchApiRequest('/ride/'. $rideId, [
            'driverId' => $driverId,
            'eventId' => RideEventType::ACCEPTED_ID
        ]);
        $this->seeResponseContainsJson([
            'driver' => [
                'id' => $driverId
            ],
            'passenger' => [
                'id' => $passengerId
            ]
        ]);
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
        $destinationLat = LocationServiceTest::WORK_LOCATION_LAT;
        $destinationLong = LocationServiceTest::WORK_LOCATION_LONG;
        $this->assignDestinationToRide($rideId, $destinationLat, $destinationLong);
    }

    public function getNewRide()
    {
        $passenger = $this->getNewPassenger();

        $createdRide = $this->sendPostApiRequest('/ride', [
            'passengerId' => $passenger['id'],
            'departureLat' => LocationServiceTest::HOME_LOCATION_LAT,
            'departureLong' => LocationServiceTest::HOME_LOCATION_LONG
        ]);
        $rideId = $createdRide['id'];

        $this->sendGetApiRequest('/ride/'.$rideId);
        $this->seeResponseContainsJson([
            'id' => $rideId
        ]);

        $statusIdToVerify = RideEventType::REQUESTED_ID;
        $statusNameToVerify = RideEventType::REQUESTED;
        $this->verifyRideStatus($rideId, $statusIdToVerify, $statusNameToVerify);
        return $createdRide;
    }

    public function getNewPassenger()
    {
        $newUser = $this->createNewUser('Joe', 'Passenger');
        $userId = $newUser['id'];

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

        $retrievedUser = $this->retrieveUser($userId);
        $userId = $retrievedUser['id'];

        $patchedUser = $this->assignDriverRoleToUser($userId);
        $this->verifyDriverRoleOnUser();
        return $patchedUser;
    }

    protected function verifyPassengerRoleOnUser()
    {
        $this->verifyPassengerRole(
            2,
            \AppBundle\Entity\AppRole::PASSENGER
        );
    }

    protected function verifyDriverRoleOnUser()
    {
        $this->verifyPassengerRole(
            1,
            \AppBundle\Entity\AppRole::DRIVER
        );
    }

    /**
     * @param $userId
     * @return mixed
     */
    protected function assignPassengerRoleToUser($userId)
    {
        $this->wantTo('Assign Passenger Role to the Created User');
        $roleToAssign = \AppBundle\Entity\AppRole::PASSENGER;

        return $this->assignRoleToUser($userId, $roleToAssign);
    }

    /**
     * @param $userId
     * @return mixed
     */
    protected function assignDriverRoleToUser($userId)
    {
        $this->wantTo('Assign Driver Role to the Created User');
        $roleToAssign = \AppBundle\Entity\AppRole::DRIVER;

        return $this->assignRoleToUser($userId, $roleToAssign);
    }

    protected function createNewUser(string $first, string $last)
    {
        $response = $this->sendPostApiRequest('/user', [
            'firstName' => $first,
            'lastName' => $last
        ]);
        return $response;
    }

    /**
     * @param $userId
     * @return mixed
     */
    protected function retrieveUser($userId)
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
     * @param $roleIdToVerify
     * @param $roleToVerify
     */
    private function verifyPassengerRole($roleIdToVerify, $roleToVerify): void
    {
        $this->seeResponseContainsJson(
            [
                'roles' => [
                    [
                        'id' => $roleIdToVerify,
                        'name' => $roleToVerify
                    ]
                ]
            ]
        );
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
}
