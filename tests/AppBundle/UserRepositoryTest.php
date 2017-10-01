<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;

class UserRepositoryTest extends AppTestCase
{

    private $userRepository;

    public function testCreateAndSaveNewUser()
    {
        $this->userRepository = new UserRepository($this->em());

        $user = new AppUser('chris', 'holland');

        $this->userRepository->save($user);

        self::assertGreaterThan(0, $user->getId());
    }

}
