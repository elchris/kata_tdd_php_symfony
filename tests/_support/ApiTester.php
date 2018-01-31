<?php

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
                'roles' => [
                    [
                        'id' => 2,
                        'name' => \AppBundle\Entity\AppRole::PASSENGER
                    ]
                ]
            ]
        );
    }

    protected function verifyDriverRoleOnUser()
    {
        $this->seeResponseContainsJson(
            [
                'roles' => [
                    [
                        'id' => 1,
                        'name' => \AppBundle\Entity\AppRole::DRIVER
                    ]
                ]
            ]
        );
    }


    /**
     * @param $userId
     * @return mixed
     */
    protected function assignPassengerRoleToUser($userId)
    {
        $this->wantTo('Assign Passenger Role to the Created User');
        $patchedUser = $this->sendPatchApiRequest(
            '/user/' . $userId,
            [
                'role' => \AppBundle\Entity\AppRole::PASSENGER
            ]
        );

        return $patchedUser;
    }

    /**
     * @param $userId
     * @return mixed
     */
    protected function assignDriverRoleToUser($userId)
    {
        $this->wantTo('Assign Driver Role to the Created User');
        $patchedUser = $this->sendPatchApiRequest(
            '/user/' . $userId,
            [
                'role' => \AppBundle\Entity\AppRole::DRIVER
            ]
        );

        return $patchedUser;
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
}
