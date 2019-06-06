<?php

namespace AppBundle\Controller;

use AppBundle\Dto\UserDto;
use AppBundle\Entity\AppRole;
use AppBundle\Exception\InvalidRoleException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AppController
{
    /**
     * @Rest\Post("/api/v1/user")
     * @param Request $request
     * @return UserDto
     * @throws Exception
     */
    public function newUser(Request $request) : UserDto
    {
        return $this->userService()->register(
            $request->get('first'),
            $request->get('last')
        )->toDto();
    }

    /**
     * @Rest\Patch("/api/v1/user/{userId}")
     * @param string $userId
     * @param Request $request
     * @return UserDto
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws InvalidRoleException
     */
    public function patchUser(string $userId, Request $request)
    {
        $userToPatch =
            $this
                ->userService()
                ->byId(
                    $this->id($userId)
                );

        $roleToAssign = AppRole::fromName($request->get('role'));

        return $this->userService()->assignRoleToUser(
            $userToPatch,
            $roleToAssign
        )->toDto();
    }
}
