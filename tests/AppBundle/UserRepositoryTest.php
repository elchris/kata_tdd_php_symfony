<?php


namespace AppBundle;

use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;
use Tests\AppBundle\AppTestCase;

class UserRepositoryTest extends AppTestCase
{
    /** @var  UserRepository */
    private $userRepository;

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
