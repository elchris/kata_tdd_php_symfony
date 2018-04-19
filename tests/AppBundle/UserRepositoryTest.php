<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class UserRepositoryTest extends AppTestCase
{
    /** @var UserRepository */
    private $userRepository;

    public function setUp()
    {
        parent::setUp();
        $this->userRepository = new UserRepository($this->em());
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function testCreateNewUser()
    {
        $retrievedUser = $this->getRepoSavedNewUser();

        self::assertTrue($retrievedUser->isNamed('chris holland'));
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function testAssignPassengerRoleToUser()
    {
        $newUser = $this->getRepoSavedNewUser();

        $this->userRepository->assignRoleToUser(
            $newUser,
            AppRole::passenger()
        );

        $retrievedUser = $this->userRepository->getById($newUser->getId());

        self::assertTrue($retrievedUser->hasAppRole(AppRole::passenger()));
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function testAssignDriverRoleToUser()
    {
        $newUser = $this->getRepoSavedNewUser();

        $this->userRepository->assignRoleToUser(
            $newUser,
            AppRole::driver()
        );

        $retrievedUser = $this->userRepository->getById($newUser->getId());

        self::assertTrue($retrievedUser->hasAppRole(AppRole::driver()));
    }

    /**
     * @return AppUser
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    protected function getRepoSavedNewUser(): AppUser
    {
        $newUser = new AppUser('chris', 'holland');
        self::assertNotNull($newUser->getId());

        $this->userRepository->saveUser($newUser);

        $retrievedUser = $this->userRepository->getById($newUser->getId());

        return $retrievedUser;
    }
}
