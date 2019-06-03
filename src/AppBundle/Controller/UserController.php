<?php

namespace AppBundle\Controller;

use AppBundle\Dto\UserDto;
use AppBundle\Entity\AppRole;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;

class UserController extends AppController
{
    /**
     * @Rest\Post("/api/v1/user")
     * @param Request $request
     * @return UserDto
     * @throws \Exception
     */
    public function registerNewUser(Request $request) : UserDto
    {
        return $this->userService()->registerNewUser(
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
        $userToPatch = $this->userService()->byId(
            $this->id($userId)
        );
        $roleName = $request->get('role');
        $role = AppRole::fromName($roleName);

        return $this->userService()->assignRoleToUser(
            $userToPatch,
            $role
        )->toDto();
    }
}
