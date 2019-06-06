<?php

namespace Tests\AppBundle\User;

use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;
use AppBundle\Service\UserSvc;
use Exception;
use Tests\AppBundle\AppTestCase;

class UserServiceTest extends AppTestCase
{
    /**
     * @throws Exception
     */
    public function testRegisterNewUser()
    {
        $userService = new UserSvc(
            new UserRepository($this->em())
        );

        /** @var AppUser $newUser */
        $newUser = $userService->register('chris', 'holland');
        $retrievedUser = $userService->byId($newUser->getId());

        self::assertTrue($retrievedUser->is($newUser));
    }
}
