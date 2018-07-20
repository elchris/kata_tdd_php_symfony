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
        self::assertFalse($retrievedUser->isNamed('rogue'));
    }

    public function testAssignPassengerRoleToUser()
    {
        $user = $this->getRepoNewUser();

        $user->assignRole($this->userRepository->getRole(AppRole::passenger()));
        $this->userRepository->saveUser($user);
        $retrievedUser = $this->userRepository->byId($user->getId());

        self::assertTrue($retrievedUser->hasAppRole(AppRole::passenger()));
        self::assertFalse($retrievedUser->hasAppRole(AppRole::driver()));
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

        return $this->userRepository->byId($newUser->getId());
    }
}
