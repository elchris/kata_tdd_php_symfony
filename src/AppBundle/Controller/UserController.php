<?php

namespace AppBundle\Controller;

use AppBundle\DTO\UserDto;
use AppBundle\Entity\AppRole;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
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
        return $this->user()->register(
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
     */
    public function patchUserRole(string $userId, Request $request) : UserDto
    {
        $userToPatch = $this->user()->byId($this->id($userId));
        $roleStringToAssign = $request->get('role');
        $roleToAssign = AppRole::fromName($roleStringToAssign);
        return $this->user()->assignRoleToUser(
            $userToPatch,
            $roleToAssign
        )->toDto();
    }
}
