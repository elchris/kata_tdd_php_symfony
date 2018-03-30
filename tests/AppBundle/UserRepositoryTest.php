<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;
use AppBundle\Entity\AppRole;

class UserRepositoryTest extends AppTestCase
{
    /** @var UserRepository */
    private $userRepository;

    public function setUp()
    {
        parent::setUp();
        $this->save(AppRole::passenger());
        $this->save(AppRole::driver());
        $this->userRepository = new UserRepository($this->em());
    }

    public function testCreateAndRetrieveUser()
    {
        $retrievedUser = $this->getSavedUser();

        self::assertTrue($retrievedUser->isNamed('Dan Fritcher'));
    }

    public function testAssignPassengerRoleToUser()
    {
        $savedUser = $this->getSavedUser();

        $this->userRepository->assignRoleToUser($savedUser, AppRole::passenger());
        $retrievedUser = $this->userRepository->getUserById($savedUser->getId());

        self::assertTrue($retrievedUser->hasAppRole(AppRole::passenger()));
    }

    /**
     * @return AppUser
     */
    protected function getSavedUser()
    {
        $userToSave = new AppUser('Dan', 'Fritcher');
        self::assertNotNull($userToSave->getId());

        $this->userRepository->saveUser($userToSave);

        $retrievedUser = $this->userRepository->getUserById($userToSave->getId());
        self::assertSame($userToSave->getId(), $retrievedUser->getId());

        return $retrievedUser;
    }
}
