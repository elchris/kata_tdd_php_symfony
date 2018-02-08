<?php

use AppBundle\Entity\AppRole;
use AppBundle\Entity\RideEventType;
use Codeception\Util\HttpCode;
use Tests\AppBundle\Production\LocationApi;

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
        $this->seeResponseCodeIs(HttpCode::OK);
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
        $roleToAssign = AppRole::PASSENGER;

        return $this->assignRoleToUser($userId, $roleToAssign);
    }

    /**
     * @param $userId
     * @return mixed
     */
    protected function assignDriverRoleToUser($userId)
    {
        $roleToAssign = AppRole::DRIVER;

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
}
