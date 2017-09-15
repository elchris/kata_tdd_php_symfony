<?php


namespace AppBundle;

use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;
use Tests\AppBundle\AppTestCase;

class UserRepositoryTest extends AppTestCase
{
    private $userRepository;

    public function testSaveNewUser()
    {
        $newUser = new AppUser('chris', 'holland');

        $this->userRepository = new UserRepository($this->em());

        $this->userRepository->save($newUser);

        self::assertGreaterThan(0, $newUser->getId());
    }
}
