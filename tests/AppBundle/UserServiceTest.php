<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;
use AppBundle\Service\UserService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class UserServiceTest extends AppTestCase
{
    /** @var UserService */
    private $userService;

    public function setUp()
    {
        parent::setUp();
        $this->userService = new UserService(new UserRepository($this->em()));
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function testCreateNewUser()
    {
        $retrievedUser = $this->getServiceNewUser();

        self::assertTrue($retrievedUser->isNamed('chris holland'));
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function testAssignPassengerRoleToUser()
    {
        $newUser = $this->getServiceNewUser();
        $this->userService->makeUserPassenger($newUser);

        $retrievedUser = $this->userService->getById($newUser->getId());

        self::assertTrue($retrievedUser->hasAppRole(AppRole::passenger()));
        self::assertFalse($retrievedUser->hasAppRole(AppRole::driver()));
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function testAssignDriverRoleToUser()
    {
        $newUser = $this->getServiceNewUser();
        $this->userService->makeUserDriver($newUser);

        $retrievedUser = $this->userService->getById($newUser->getId());

        self::assertTrue($retrievedUser->hasAppRole(AppRole::driver()));
        self::assertFalse($retrievedUser->hasAppRole(AppRole::passenger()));
    }

    /**
     * @return AppUser
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    protected function getServiceNewUser(): AppUser
    {
        $createdUser = $this->userService->newUser('chris', 'holland');
        $retrievedUser = $this->userService->getById($createdUser->getId());

        return $retrievedUser;
    }
}
