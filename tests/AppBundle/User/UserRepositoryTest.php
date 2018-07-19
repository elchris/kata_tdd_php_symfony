<?php

namespace Tests\AppBundle\User;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;
use Tests\AppBundle\AppTestCase;

class UserRepositoryTest extends AppTestCase
{

    /**
     * @var UserRepository $userRepo
     */
    private $userRepo;

    public function setUp()
    {
        parent::setUp();
        $this->userRepo = new UserRepository($this->em());
    }

    public function testCreateNewUser()
    {
        $retrievedUser = $this->getRepoNewUser();

        self::assertTrue($retrievedUser->isNamed('chris holland'));
        self::assertFalse($retrievedUser->isNamed('fake name'));
    }

    public function testAssignPassengerRoleToUser()
    {

        $retrievedUser = $this->getRepoUserAssignedRole(AppRole::passenger());

        self::assertTrue($retrievedUser->hasAppRole(AppRole::passenger()));
        self::assertFalse($retrievedUser->hasAppRole(AppRole::driver()));
    }

    public function testAssignDriverRoleToUser()
    {
        $retrievedUser = $this->getRepoUserAssignedRole(AppRole::driver());

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

        $this->userRepo->saveUser($newUser);
        $this->em()->clear();
        $retrievedUser = $this->userRepo->byId($newUser->getId());

        return $retrievedUser;
    }

    /**
     * @param $role
     * @return AppUser
     */
    protected function getRepoUserAssignedRole($role): AppUser
    {
        $user = $this->getRepoNewUser();
        $user->assignRole($this->userRepo->getRole($role));
        $this->userRepo->saveUser($user);
        $retrievedUser = $this->userRepo->byId($user->getId());

        return $retrievedUser;
    }
}
