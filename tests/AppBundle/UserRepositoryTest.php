<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;

class UserRepositoryTest extends AppTestCase
{

    /** @var  UserRepository */
    private $userRepository;

    public function setUp()
    {
        parent::setUp();
        $this->userRepository = new UserRepository($this->em());

        $this->save(AppRole::driver());
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
    }

    public function testAssignDriverRoleToUser()
    {
        $this->assertUserHasExpectedRole(AppRole::driver());
    }

    public function testAssignPassengerRoleToUser()
    {
        $this->assertUserHasExpectedRole(AppRole::passenger());
    }

    /**
     * @return AppUser
     */
    private function getSavedUser()
    {
        $user = new AppUser('chris', 'holland');

        $this->userRepository->save($user);

        return $user;
    }

    /**
     * @param $role
     */
    private function assertUserHasExpectedRole($role)
    {
        $savedUser = $this->getSavedUser();

        $this->userRepository->assignRoleToUser($savedUser, $role);
        $retrievedUser = $this->userRepository->getUserById($savedUser->getId());

        self::assertTrue($this->userRepository->userHasRole($retrievedUser, $role));
    }
}
