<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Exception\DuplicateRoleAssignmentException;

class UserRepositoryTest extends AppTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testCreateAndSaveNewUser()
    {
        $user = $this->getSavedUser();

        self::assertNotEmpty($user->getId());
    }

    public function testGetUserById()
    {
        $savedUser = $this->getSavedUser();

        $retrievedUser = $this->getServiceUserById($savedUser->getId());

        self::assertSame($savedUser->getId(), $retrievedUser->getId());
        self::assertTrue($savedUser->is($retrievedUser));
    }

    public function testAssignDriverRoleToUser()
    {
        $this->assertUserHasExpectedRole(AppRole::driver());
    }

    public function testAssignPassengerRoleToUser()
    {
        $this->assertUserHasExpectedRole(AppRole::passenger());
    }

    public function testUserCanHaveBothRoles()
    {
        $savedUser = $this->getSavedUser();

        $this->assignRoleToUser($savedUser, AppRole::driver());
        $this->assignRoleToUser($savedUser, AppRole::passenger());

        $retrievedUser = $this->getServiceUserById($savedUser->getId());

        self::assertTrue($retrievedUser->hasRole(AppRole::driver()));
        self::assertTrue($retrievedUser->hasRole(AppRole::passenger()));
    }

    public function testDuplicateRoleAssignmentThrows()
    {
        $savedUser = $this->getSavedUser();

        $this->assignRoleToUser($savedUser, AppRole::driver());
        $this->expectException(DuplicateRoleAssignmentException::class);

        $this->assignRoleToUser($savedUser, AppRole::driver());
    }

    private function assertUserHasExpectedRole(AppRole $role)
    {
        $savedUser = $this->getSavedUser();

        $this->assignRoleToUser($savedUser, $role);
        $retrievedUser = $this->userRepository->getUserById($savedUser->getId());

        self::assertTrue($retrievedUser->hasRole($role));
    }

    protected function assignRoleToUser(AppUser $user, AppRole $role)
    {
        $this->userRepository->assignRoleToUser($user, $role);
    }
}
