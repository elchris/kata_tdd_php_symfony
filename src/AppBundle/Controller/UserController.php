<?php

namespace AppBundle\Controller;

use AppBundle\Dto\UserDto;
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
}
