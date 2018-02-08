<?php

namespace AppBundle\Controller;

use AppBundle\DTO\UserDto;
use AppBundle\Entity\AppRole;
use AppBundle\Exception\DuplicateRoleAssignmentException;
use AppBundle\Exception\UserNotFoundException;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AppController
{
    /**
     * @Rest\Post("/api/v1/user")
     * @param Request $request
     * @return UserDto
     */
    public function postAction(Request $request)
    {
        $createdUser = $this->user()->newUser(
            $request->get('firstName'),
            $request->get('lastName')
        );

        return new UserDto($createdUser);
    }

    /**
     * @Rest\Get("/api/v1/user/{id}")
     * @param string $id
     * @return UserDto
     * @throws UserNotFoundException
     */
    public function idAction(string $id)
    {
        return new UserDto($this->getUserById($id));
    }

    /**
     * @Rest\Patch("/api/v1/user/{id}")
     * @param string $id
     * @param Request $request
     * @return UserDto
     * @throws UserNotFoundException
     * @throws DuplicateRoleAssignmentException
     */
    public function patchAction(string $id, Request $request)
    {
        $userToPatch = $this->getUserById($id);
        $this->patchRole($request, $userToPatch);
        return new UserDto($userToPatch);
    }

    /**
     * @param Request $request
     * @param $userToPatch
     * @throws DuplicateRoleAssignmentException
     */
    private function patchRole(Request $request, $userToPatch): void
    {
        $roleToAssign = $request->get('role');
        if (AppRole::isPassenger($roleToAssign)) {
            $this->user()->makeUserPassenger($userToPatch);
        } elseif (AppRole::isDriver($roleToAssign)) {
            $this->user()->makeUserDriver($userToPatch);
        }
    }
}
