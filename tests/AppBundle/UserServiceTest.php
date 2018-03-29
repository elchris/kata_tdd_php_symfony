<?php

namespace Tests\AppBundle;

use AppBundle\Repository\UserRepository;
use AppBundle\Entity\AppUser;
use AppBundle\Service\UserService;

class UserServiceTest extends AppTestCase
{
    public function testCreateUser()
    {
        $userService = new UserService(new UserRepository($this->em()));

        /** @var AppUser $newUser */
        $newUser = $userService->newUser('Dan', 'Fritcher');

        self::assertTrue($newUser->isNamed('Dan Fritcher'));
    }
}
