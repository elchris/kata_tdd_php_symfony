<?php


namespace AppBundle\Controller;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\UserDto;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;

class UserController extends AppController
{
    /**
     * @Rest\Post("/api/v1/register-user")
     * @param Request $request
     * @return UserDto
     * @throws \Exception
     */
    public function registerUserPost(Request $request) : UserDto
    {
        return $this->user()->registerUser(
            $request->get('first'),
            $request->get('last')
        )->toDto();
    }

    /**
     * @Rest\Patch("/api/v1/user/{userId}")
     * @param string $userId
     * @param Request $request
     * @return UserDto
     */
    public function patchUser(string $userId, Request $request) : UserDto
    {
        $userToPatch = $this->user()->byId($this->id($userId));
        $roleNameToAssign = $request->get('role');

        return $this->user()->assignRoleToUser(
            $userToPatch,
            AppRole::fromName($roleNameToAssign)
        )->toDto();
    }
}
