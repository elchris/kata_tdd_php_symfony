<?php

namespace AppBundle\Controller;

use AppBundle\Dto\UserDto;
use AppBundle\Entity\AppRole;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;

class UserController extends AppController
{
    /**
     * @Rest\Post("/api/v1/register-user")
     * @param Request $request
     * @return UserDto
     */
    public function postAction(Request $request) : UserDto
    {
        return $this->user()->newUser(
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
    public function patchAction(string $userId, Request $request) : UserDto
    {
        $userToPatch = $this->user()->byId($this->id($userId));
        $roleToPatch = $request->get('role');

        return $this->user()->assignRoleToUser(
            $userToPatch,
            AppRole::fromKey($roleToPatch)
        )->toDto();
    }
}
