<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppRole;
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

        self::assertGreaterThan(0, $user->getId());
    }

    public function testGetUserById()
    {
        $savedUser = $this->getSavedUser();

        $retrievedUser = $this->userRepository->getUserById(1);

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

        $this->userRepository->assignRoleToUser($savedUser, AppRole::driver());
        $this->userRepository->assignRoleToUser($savedUser, AppRole::passenger());

        $retrievedUser = $this->userRepository->getUserById($savedUser->getId());

        self::assertTrue($retrievedUser->hasRole(AppRole::driver()));
        self::assertTrue($retrievedUser->hasRole(AppRole::passenger()));
    }

    public function testDuplicateRoleAssignmentThrows()
    {
        $savedUser = $this->getSavedUser();

        $this->userRepository->assignRoleToUser($savedUser, AppRole::driver());
        $this->expectException(DuplicateRoleAssignmentException::class);

        $this->userRepository->assignRoleToUser($savedUser, AppRole::driver());
    }

    private function assertUserHasExpectedRole(AppRole $role)
    {
        $savedUser = $this->getSavedUser();

        $this->userRepository->assignRoleToUser($savedUser, $role);
        $retrievedUser = $this->userRepository->getUserById($savedUser->getId());

        self::assertTrue($retrievedUser->hasRole($role));
    }
}
