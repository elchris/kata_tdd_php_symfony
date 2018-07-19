<?php

namespace Tests\AppBundle\User;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;
use Tests\AppBundle\AppTestCase;

class UserRepositoryTest extends AppTestCase
{
    /**
     * @var UserRepository $userRepository
     */
    private $userRepository;

    public function setUp()
    {
        parent::setUp();
        $this->userRepository = new UserRepository($this->em());
    }

    public function testCreateNewUser()
    {
        $retrievedUser = $this->getRepoNewUser();

        self::assertTrue($retrievedUser->isNamed('chris holland'));
        self::assertFalse($retrievedUser->isNamed('rogue user'));
    }

    public function testAssignPassengerRoleToUser()
    {
        $user = $this->getRepoNewUser();

        $retrievedUser = $this->getRepoUserWithRole($user, AppRole::passenger());

        self::assertTrue($retrievedUser->hasAppRole(AppRole::passenger()));
        self::assertFalse($retrievedUser->hasAppRole(AppRole::driver()));
    }

    public function testAssignDriverRoleToUser()
    {
        $user = $this->getRepoNewUser();

        $retrievedUser = $this->getRepoUserWithRole($user, AppRole::driver());

        self::assertTrue($retrievedUser->hasAppRole(AppRole::driver()));
        self::assertFalse($retrievedUser->hasAppRole(AppRole::passenger()));
    }

    /**
     * @return AppUser
     */
    protected function getRepoNewUser(): AppUser
    {
        $newUser = new AppUser('chris', 'holland');
        self::assertNotNull($newUser->getId());

        $this->userRepository->saveUser($newUser);
        $this->em()->clear();
        /** @var AppUser $retrievedUser */
        $retrievedUser = $this->userRepository->getById($newUser->getId());

        return $retrievedUser;
    }

    /**
     * @param $user
     * @param $roleToAssign
     * @return AppUser
     */
    protected function getRepoUserWithRole($user, $roleToAssign): AppUser
    {
        $user->assignRole($this->userRepository->getRole($roleToAssign));
        $this->userRepository->saveUser($user);
        /** @var AppUser $retrievedUser */
        $retrievedUser = $this->userRepository->getById($user->getId());

        return $retrievedUser;
    }
}
