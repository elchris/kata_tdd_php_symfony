<?php

namespace Tests\AppBundle;

use AppBundle\Repository\UserRepository;
use AppBundle\Service\UserService;

class UserServiceTest extends AppTestCase
{
    public function testCreateAndReturnNewUser()
    {
        $userService = new UserService(
            new UserRepository($this->em())
        );

        $createdUser = $userService->newUser(
            'Dan',
            'Fritcher'
        );

        self::assertTrue($createdUser->isNamed('Dan Fritcher'));
    }
}
