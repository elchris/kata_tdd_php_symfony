<?php

namespace AppBundle\Controller;

use AppBundle\Dto\UserDto;
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
}
