<?php

namespace Tests\AppBundle\User;

use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;
use Tests\AppBundle\AppTestCase;

class UserRepositoryTest extends AppTestCase
{
    public function testCreateNewUser()
    {
        $newUser = new AppUser('chris', 'holland');
        self::assertNotNull($newUser->getId());

        $userRepository = new UserRepository($this->em());
        $userRepository->saveAndGet($newUser);
        $this->em()->clear();
        /** @var AppUser $retrievedUser */
        $retrievedUser = $userRepository->byId($newUser->getId());

        self::assertTrue($retrievedUser->isNamed('chris holland'));
    }
}
