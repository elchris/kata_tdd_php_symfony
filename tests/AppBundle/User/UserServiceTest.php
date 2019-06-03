<?php

namespace Tests\AppBundle\User;

use AppBundle\Entity\AppRole;
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
        $registeredUser = $this->getSvcNewUser();
        $retrievedUser = $this->userService->byId($registeredUser->getId());

        self::assertTrue($retrievedUser->is($registeredUser));
    }

    /**
     * @throws Exception
     */
    public function testAssignRoleToUser()
    {
        $user = $this->getSvcNewUser();

        $this->userService->assignRoleToUser($user, AppRole::passenger());
        /** @var AppUser $retrievedUser */
        $retrievedUser = $this->userService->assignRoleToUser($user, AppRole::driver());

        self::assertTrue($retrievedUser->hasRole(AppRole::passenger()));
        self::assertTrue($retrievedUser->hasRole(AppRole::driver()));
    }

    /**
     * @return AppUser
     * @throws Exception
     */
    private function getSvcNewUser(): AppUser
    {
        return $this->userService->registerNewUser('chris', 'holland');
    }
}
