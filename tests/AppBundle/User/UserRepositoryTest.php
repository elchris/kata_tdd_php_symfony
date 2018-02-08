<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Exception\DuplicateRoleAssignmentException;
use AppBundle\Exception\UserNotFoundException;
use Ramsey\Uuid\Uuid;

class UserRepositoryTest extends AppTestCase
{
    /**
     * @throws UserNotFoundException
     */
    public function testUserUuid()
    {
        $user = $this->user()->getSavedUser();
        $this->em()->clear();
        $retrievedUser = $this->user()->getRepo()->getUserById($user->getId());
        self::assertTrue($retrievedUser->is($user));
    }

    public function testCreateAndSaveNewUser()
    {
        $user = $this->user()->getSavedUser();

        self::assertNotNull($user);
    }

    /**
     * @throws UserNotFoundException
     */
    public function testGetUserById()
    {
        $savedUser = $this->user()->getSavedUser();

        $retrievedUser = $this->getRepoUserById($savedUser->getId());

        self::assertTrue($savedUser->is($retrievedUser));
    }

    /**
     * @throws UserNotFoundException
     */
    public function testBadUserIdThrowsUserNotFoundException()
    {
        /** @var Uuid $nonExistentId */
        $nonExistentId = Uuid::uuid4();

        $this->expectException(UserNotFoundException::class);
        $this->getRepoUserById($nonExistentId);
    }

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     */
    public function testAssignDriverRoleToUser()
    {
        $this->assertUserHasExpectedRole(AppRole::driver());
    }

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     */
    public function testAssignPassengerRoleToUser()
    {
        $this->assertUserHasExpectedRole(AppRole::passenger());
    }

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     */
    public function testUserCanHaveBothRoles()
    {
        $savedUser = $this->user()->getSavedUser();

        $this->assignRepoRoleToUser($savedUser, AppRole::driver());
        $this->assignRepoRoleToUser($savedUser, AppRole::passenger());

        $retrievedUser = $this->getRepoUserById($savedUser->getId());

        self::assertTrue($retrievedUser->hasRole(AppRole::driver()));
        self::assertTrue($retrievedUser->hasRole(AppRole::passenger()));
    }

    /**
     * @throws DuplicateRoleAssignmentException
     */
    public function testDuplicateRoleAssignmentThrows()
    {
        $savedUser = $this->user()->getSavedUser();

        $this->assignRepoRoleToUser($savedUser, AppRole::driver());
        $this->expectException(DuplicateRoleAssignmentException::class);

        $this->assignRepoRoleToUser($savedUser, AppRole::driver());
    }

    /**
     * @param AppRole $role
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     */
    private function assertUserHasExpectedRole(AppRole $role)
    {
        $savedUser = $this->user()->getSavedUser();

        $this->assignRepoRoleToUser($savedUser, $role);
        $retrievedUser = $this->getRepoUserById($savedUser->getId());

        self::assertTrue($retrievedUser->hasRole($role));
    }

    /**
     * @param AppUser $user
     * @param AppRole $role
     * @throws DuplicateRoleAssignmentException
     */
    protected function assignRepoRoleToUser(AppUser $user, AppRole $role)
    {
        $this->user()->getRepo()->assignRoleToUser($user, $role);
    }

    /**
     * @param Uuid $userId
     * @return AppUser
     * @throws UserNotFoundException
     */
    private function getRepoUserById(Uuid $userId): AppUser
    {
        return $this->user()->getRepo()->getUserById($userId);
    }
}
