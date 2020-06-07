<?php

namespace Tests\AppBundle\User;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Exception\DuplicateRoleAssignmentException;
use AppBundle\Exception\UserNotFoundException;
use Exception;
use Ramsey\Uuid\Uuid;
use Tests\AppBundle\AppTestCase;

class UserRepositoryTest extends AppTestCase
{
    /**
     * @throws UserNotFoundException
     */
    public function testUserUuid(): void
    {
        $user = $this->user()->getSavedUser();
        $this->em()->clear();
        $retrievedUser = $this->user()->getRepo()->getUserById($user->getId());
        self::assertTrue($retrievedUser->is($user));
    }

    public function testCreateAndSaveNewUser(): void
    {
        $user = $this->user()->getSavedUser();

        self::assertNotNull($user);
    }

    /**
     * @throws UserNotFoundException
     */
    public function testGetUserById(): void
    {
        $savedUser = $this->user()->getSavedUser();

        $retrievedUser = $this->getRepoUserById($savedUser->getId());

        self::assertTrue($savedUser->is($retrievedUser));
    }

    /**
     * @throws UserNotFoundException
     * @throws Exception
     */
    public function testBadUserIdThrowsUserNotFoundException(): void
    {
        /** @var Uuid $nonExistentId */
        $nonExistentId = Uuid::uuid4();

        $this->verifyExceptionWithMessage(
            UserNotFoundException::class,
            UserNotFoundException::MESSAGE
        );
        $this->getRepoUserById($nonExistentId);
    }

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     */
    public function testAssignDriverRoleToUser(): void
    {
        $this->assertUserHasExpectedRole(AppRole::driver());
    }

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     */
    public function testAssignPassengerRoleToUser(): void
    {
        $this->assertUserHasExpectedRole(AppRole::passenger());
    }

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     */
    public function testUserCanHaveBothRoles(): void
    {
        $savedUser = $this->user()->getSavedUser();

        $this->assignRepoRoleToUser($savedUser, AppRole::driver());
        $this->assignRepoRoleToUser($savedUser, AppRole::passenger());

        $retrievedUser = $this->getRepoUserById($savedUser->getId());

        self::assertTrue($retrievedUser->userHasRole(AppRole::driver()));
        self::assertTrue($retrievedUser->userHasRole(AppRole::passenger()));
    }

    /**
     * @throws DuplicateRoleAssignmentException
     */
    public function testDuplicateRoleAssignmentThrows(): void
    {
        $savedUser = $this->user()->getSavedUser();

        $this->assignRepoRoleToUser($savedUser, AppRole::driver());
        $this->verifyExceptionWithMessage(
            DuplicateRoleAssignmentException::class,
            DuplicateRoleAssignmentException::MESSAGE
        );

        $this->assignRepoRoleToUser($savedUser, AppRole::driver());
    }

    /**
     * @param AppRole $role
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     */
    private function assertUserHasExpectedRole(AppRole $role): void
    {
        $savedUser = $this->user()->getSavedUser();

        $this->assignRepoRoleToUser($savedUser, $role);
        $retrievedUser = $this->getRepoUserById($savedUser->getId());

        self::assertTrue($retrievedUser->userHasRole($role));
    }

    /**
     * @param AppUser $user
     * @param AppRole $role
     * @throws DuplicateRoleAssignmentException
     */
    protected function assignRepoRoleToUser(AppUser $user, AppRole $role): void
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
