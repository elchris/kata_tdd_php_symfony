<?php

namespace Tests\AppBundle\User;

use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Tests\AppBundle\AppTestCase;

class UserRepositoryTest extends AppTestCase
{

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function testCreateNewUser()
    {
        $newUser = new AppUser('chris', 'holland');
        self::assertNotNull($newUser->getId());

        $savedUser = $this->userRepository->saveUser($newUser);
        /** @var AppUser $retrievedUser */
        $this->em()->clear();
        $retrievedUser = $this->userRepository->byId($newUser->getId());

        self::assertTrue($retrievedUser->isNamed('chris holland'));
    }
}
