<?php

namespace AppBundle\Controller;

use AppBundle\DTO\UserDto;
use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;
use AppBundle\Service\UserService;
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
     */
    public function postAction(Request $request) : UserDto
    {
        $first = $request->get('first');
        $last = $request->get('last');

        $newUser = $this->user()->newUser($first, $last);

        return $newUser->toDto();
    }

    /**
     * @Rest\Patch("/api/v1/user/{userId}")
     * @param string $userId
     * @param Request $request
     * @return AppUser
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function patchAction(string $userId, Request $request) : UserDto
    {
        $userToPatch = $this
            ->user()
            ->getById($this->id($userId));

        $roleToPatch = $request->get('role');
        if ($roleToPatch === AppRole::PASSENGER) {
            $this->user()->makeUserPassenger($userToPatch);
        } elseif ($roleToPatch === AppRole::DRIVER) {
            $this->user()->makeUserDriver($userToPatch);
        }

        $patchedUser = $this->user()->getById($this->id($userId));

        return $patchedUser->toDto();
    }
}
