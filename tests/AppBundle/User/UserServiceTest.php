<?php

namespace Tests\AppBundle\User;

use AppBundle\Entity\AppUser;
use Exception;
use Tests\AppBundle\AppTestCase;

class UserServiceTest extends AppTestCase
{
    /**
     * @throws Exception
     */
    public function testRegisterNewUser()
    {
        /** @var AppUser $newUser */
        $newUser = $this->userService->register('chris', 'holland');
        $retrievedUser = $this->userService->byId($newUser->getId());

        self::assertTrue($retrievedUser->is($newUser));
    }
}
