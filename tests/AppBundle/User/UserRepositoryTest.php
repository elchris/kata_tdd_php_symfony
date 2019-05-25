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
        $newUser = new AppUser(
            'chris',
            'holland'
        );

        $userRepo = new UserRepository($this->em());

        $userRepo->saveUser($newUser);
        /** @var AppUser $retrievedUser */
        $retrievedUser = $userRepo->byId($newUser->getId());

        self::assertTrue($retrievedUser->isNamed('chris holland'));
        self::assertTrue($retrievedUser->is($newUser));
    }
}
