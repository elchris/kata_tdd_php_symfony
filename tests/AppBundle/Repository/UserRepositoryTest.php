<?php

namespace Tests\AppBundle\Repository;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Exception\RoleLifeCycleException;
use AppBundle\Repository\UserRepository;
use Tests\AppBundle\AppTestCase;

class UserRepositoryTest extends AppTestCase
{
    /** @var UserRepository $userRepository */
    private $userRepository;

    /** @var AppRole $driverRole */
    private $driverRole;

    /** @var AppRole $passengerRole */
    private $passengerRole;

    public function setUp()
    {
        parent::setUp();
        $this->userRepository = new UserRepository($this->em());

        $this->driverRole = AppRole::driver();
        $this->save($this->driverRole);
        $this->passengerRole = AppRole::passenger();
        $this->save($this->passengerRole);
    }
    public function testOrmWorks()
    {
        $user = $this->makeSavedUser();
        self::assertGreaterThan(0, $user->getId());
    }

    public function testGetUserById()
    {
        $this->makeSavedUser();

        $testUser = $this->userRepository->getUserById(1);

        self::assertEquals(1, $testUser->getId());
    }

    public function testAddRoleToUser()
    {
        $role = AppRole::driver();

        $user = $this->makeSavedUser();
        $user->addRole($role);

        self::assertTrue($user->hasRole($role));
    }

    public function testSaveUserWithDriverRole()
    {
        $user = $this->getSavedUserWithRole($this->driverRole);

        $savedUser = $this->userRepository->getUserById(1);

        self::assertTrue($savedUser->hasRole($user->getRoles()->first()));
        self::assertCount(1, $savedUser->getRoles());
    }

    public function testSaveUserWithPassengerRole()
    {
        $role = AppRole::passenger();
        self::assertEquals(2, $role->getId());
    }

    public function testUserAlreadyHasRoleThrows()
    {
        $user = $this->getSavedUserWithRole($this->driverRole);

        self::expectException(RoleLifeCycleException::class);
        $user->addRole($user->getRoles()->first());
    }

    public function testUserHasTwoDifferentRoles()
    {
        $user = $this->getSavedUserWithRole($this->driverRole);
        $user->addRole($this->passengerRole);
        $this->userRepository->save($user);

        $savedUser = $this->userRepository->getUserById(1);

        self::assertCount(2, $savedUser->getRoles());
    }

    public function testUserHasTwoDifferentRolesWithOneDupeThrows()
    {
        $user = $this->getSavedUserWithRole($this->driverRole);
        $user->addRole($this->passengerRole);

        $this->expectException(RoleLifeCycleException::class);
        $user->addRole($this->passengerRole);
    }

    /**
     * @return AppUser
     */
    private function makeSavedUser()
    {
        $user = new AppUser();
        $this->userRepository->save($user);

        return $user;
    }

    /**
     * @param $role
     * @return AppUser
     */
    private function getSavedUserWithRole($role)
    {
        $user = $this->makeSavedUser();
        $user->addRole($role);
        $this->userRepository->save($user);

        return $user;
    }
}
