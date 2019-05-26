<?php

namespace Tests\AppBundle\User;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
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
        $retrievedUser = $this->getRepoNewUser();

        self::assertTrue($retrievedUser->isNamed('chris holland'));
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function testAssignPassengerRoleToUser()
    {
        $newUser = $this->getRepoNewPassenger();
        $this->em()->clear();
        /** @var AppUser $retrievedUser */
        $retrievedUser = $this->userRepo->byId($newUser->getId());

        self::assertTrue($retrievedUser->hasRole(AppRole::passenger()));
        self::assertFalse($retrievedUser->hasRole(AppRole::driver()));
    }
}
