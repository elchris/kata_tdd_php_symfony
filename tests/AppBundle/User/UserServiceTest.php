<?php

namespace Tests\AppBundle\User;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use Exception;
use Tests\AppBundle\AppTestCase;

class UserServiceTest extends AppTestCase
{
    /**
     * @throws Exception
     */
    public function testRegisterNewUser(): void
    {
        $newUser = $this->getSvcNewUser();
        $retrievedUser = $this->userService->byId($newUser->getId());

        self::assertTrue($retrievedUser->is($newUser));
    }

    /**
     * @throws Exception
     */
    public function testAssignRoleToUser(): void
    {
        $newUser = $this->getSvcNewUser();

        /** @var AppUser $patchedUser */
        $patchedUser = $this->userService->assignRoleToUser(
            $newUser,
            AppRole::passenger()
        );

        self::assertTrue($patchedUser->hasRole(AppRole::passenger()));
        self::assertFalse($patchedUser->hasRole(AppRole::driver()));

        $rePatchedUser = $this->userService->assignRoleToUser(
            $patchedUser,
            AppRole::driver()
        );

        self::assertTrue($rePatchedUser->hasRole(AppRole::passenger()));
        self::assertTrue($rePatchedUser->hasRole(AppRole::driver()));
    }

    /**
     * @return AppUser
     * @throws Exception
     */
    private function getSvcNewUser(): AppUser
    {
        return $this->userService->register('chris', 'holland');
    }
}
