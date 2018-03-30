<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;

class UserController extends AppController
{
    /**
     * @Rest\Post("/api/v1/register-user")
     */
    public function postAction(Request $request)
    {
        $userEntity = $this->user()->newUser(
            $request->get('first'),
            $request->get('last')
        );

        return $userEntity->toDto();
    }
}
