<?php

namespace AppBundle\Controller;

use AppBundle\DTO\UserDto;
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
}
