<?php

namespace Tests\AppBundle\User;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Tests\AppBundle\AppTestCase;

class UserRepositoryTest extends AppTestCase
{
    /** @var UserRepository */
    private $userRepo;

    public function setUp()
    {
        parent::setUp();
        $this->userRepo = new UserRepository($this->em());
    }

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
        $newUser = $this->getRepoNewUser();
        $newUser->assignRole(
            $this->userRepo->getRoleReference(
                AppRole::passenger()
            )
        );

        $this->userRepo->saveUser($newUser);
        $this->em()->clear();
        /** @var AppUser $retrievedUser */
        $retrievedUser = $this->userRepo->byId($newUser->getId());

        self::assertTrue($retrievedUser->hasRole(AppRole::passenger()));
        self::assertFalse($retrievedUser->hasRole(AppRole::driver()));
    }

    /**
     * @return AppUser
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    protected function getRepoNewUser(): AppUser
    {
        $newUser = new AppUser(
            'chris',
            'holland'
        );
        $this->userRepo->saveUser($newUser);
        /** @var AppUser $retrievedUser */
        $retrievedUser = $this->userRepo->byId($newUser->getId());
        self::assertTrue($retrievedUser->is($newUser));

        return $retrievedUser;
    }
}
