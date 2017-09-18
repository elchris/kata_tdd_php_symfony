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

    public function setUp()
    {
        parent::setUp();

        $this->editoriallySavedDriverRole = AppRole::driver();
        $this->save($this->editoriallySavedDriverRole);
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
        //Given
        $savedUser = $this->getSavedUser();

        //When
        $savedUser->assignRole($this->editoriallySavedDriverRole);
        $this->userRepository->save($savedUser);
        $retrievedUserWithDriverRole = $this->userRepository->getUserById($savedUser->getId());

        //Then
        self::assertTrue($retrievedUserWithDriverRole->hasRole($this->editoriallySavedDriverRole));
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
}
