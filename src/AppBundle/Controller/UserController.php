<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AppController
{
    /**
     * @Rest\Post("/api/v1/register-user")
     * @param Request $request
     * @return UserDto
     */
    public function postAction(Request $request)
    {
        return $this->user()->newUser(
            $request->get('first'),
            $request->get('last')
        );
    }
}
