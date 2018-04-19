<?php


namespace AppBundle\Controller;

use AppBundle\DTO\UserDto;
use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;

class UserController extends AppController
{
    /**
     * @Rest\Post("/api/v1/register-user")
     * @param Request $request
     * @return AppUser
     */
    public function postAction(Request $request) : UserDto
    {
        $newUser = $this->user()->newUser(
            $request->get('first'),
            $request->get('last')
        );

        return $newUser->toDto();
    }

    /**
     * @Rest\Get("/api/v1/user/{id}")
     * @param string $id
     * @return UserDto
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function idAction(string $id) : UserDto
    {
        $user = $this->user()->getById($this->id($id));
        return $user->toDto();
    }

    /**
     * @Rest\Patch("/api/v1/user/{id}")
     * @param string $id
     * @return UserDto
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function patchAction(string $id, Request $request) : UserDto
    {
        $user = $this->user()->getById($this->id($id));
        $patchRole = $request->get('role');
        if (AppRole::PASSENGER === $patchRole) {
            $this->user()->makeUserPassenger($user);
        } elseif (AppRole::DRIVER === $patchRole) {
            $this->user()->makeUserDriver($user);
        }
        return $user->toDto();
    }
}
