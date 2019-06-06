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
        $newUser = $this->getSvcNewUser();
        $retrievedUser = $this->userService->byId($newUser->getId());

        self::assertTrue($retrievedUser->is($newUser));
    }

//    public function testAssignRoleToUser()
//    {
//
//    }

    /**
     * @return AppUser
     * @throws Exception
     */
    private function getSvcNewUser(): AppUser
    {
        return $this->userService->register('chris', 'holland');
    }
}
