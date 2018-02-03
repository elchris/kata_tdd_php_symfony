<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AppRole;
use AppBundle\Exception\DuplicateRoleAssignmentException;
use AppBundle\Exception\UserNotFoundException;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\AppUser;

class UserController extends AppController
{
    /**
     * @Rest\Post("/api/v1/user")
     * @param Request $request
     * @return AppUser
     */
    public function postAction(Request $request)
    {
        $createdUser = $this->user()->newUser(
            $request->get('firstName'),
            $request->get('lastName')
        );

        return $createdUser;
    }

    /**
     * @Rest\Get("/api/v1/user/{id}")
     * @param string $id
     * @return AppUser
     * @throws UserNotFoundException
     */
    public function idAction(string $id)
    {
        return $this->getUserById($id);
    }

    /**
     * @Rest\Patch("/api/v1/user/{id}")
     * @param string $id
     * @param Request $request
     * @return AppUser
     * @throws UserNotFoundException
     * @throws DuplicateRoleAssignmentException
     */
    public function patchAction(string $id, Request $request)
    {
        $userToPatch = $this->getUserById($id);
        $this->patchRole($request, $userToPatch);
        return $userToPatch;
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
