<?php

namespace Tests\AppBundle\User;

use AppBundle\Entity\AppUser;
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
        /** @var AppUser $registeredUser */
        $registeredUser = $this->userService->registerNewUser('chris', 'holland');
        $retrievedUser = $this->userService->byId($registeredUser->getId());

        self::assertTrue($retrievedUser->is($registeredUser));
    }
}
