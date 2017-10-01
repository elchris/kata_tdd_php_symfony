<?php

namespace Tests\AppBundle;

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

    /**
     * @return AppUser
     */
    private function getSavedUser()
    {
        $user = new AppUser('chris', 'holland');

        $this->userRepository->save($user);

        return $user;
    }
}
