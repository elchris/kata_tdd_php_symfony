<?php


namespace AppBundle;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;
use Tests\AppBundle\AppTestCase;

class UserRepositoryTest extends AppTestCase
{
    /** @var  UserRepository */
    private $userRepository;

    /** @var  AppRole $editoriallySavedDriverRole */
    private $editoriallySavedDriverRole;

    /** @var  AppRole $editoriallySavedPassengerRole */
    private $editoriallySavedPassengerRole;

    public function setUp()
    {
        parent::setUp();

        $this->editoriallySavedDriverRole = AppRole::driver();
        $this->editoriallySavedPassengerRole = AppRole::passenger();
        $this->save($this->editoriallySavedDriverRole);
        $this->save($this->editoriallySavedPassengerRole);
    }

    public function testSaveNewUser()
    {
        $newUser = $this->getSavedUser();

        self::assertGreaterThan(0, $newUser->getId());
    }

    public function testGetUserById()
    {
        $savedUser = $this->getSavedUser();

        $retrievedUserById = $this->userRepository->getUserById($savedUser->getId());

        self::assertSame($savedUser->getId(), $retrievedUserById->getId());
    }

    public function testAssignDriverRoleToUser()
    {
        $this->assertAssignedRoleToUser($this->editoriallySavedDriverRole);
    }

    public function testAssignPassengerRoleToUser()
    {
        $this->assertAssignedRoleToUser($this->editoriallySavedPassengerRole);
    }

    /**
     * @return AppUser
     */
    protected function getSavedUser()
    {
        $newUser = new AppUser('chris', 'holland');

        $this->userRepository = new UserRepository($this->em());

        $this->userRepository->save($newUser);

        return $newUser;
    }

    /**
     * @param AppRole $roleToTest
     */
    protected function assertAssignedRoleToUser(AppRole $roleToTest)
    {
        //Given
        $savedUser = $this->getSavedUser();

        //When
        $savedUser->assignRole($roleToTest);
        $this->userRepository->save($savedUser);
        $retrievedUserWithAssignedRole = $this->userRepository->getUserById($savedUser->getId());

        //Then
        self::assertTrue($retrievedUserWithAssignedRole->hasRole($roleToTest));
    }
}
