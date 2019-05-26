<?php

namespace Tests\AppBundle\User;

use AppBundle\Entity\AppRole;
use AppBundle\Repository\UserRepository;
use AppBundle\Service\UserSvc;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Tests\AppBundle\AppTestCase;

class UserServiceTest extends AppTestCase
{
    /** @var UserSvc */
    private $userService;

    public function setUp()
    {
        parent::setUp();
        $this->userService = new UserSvc(
            new UserRepository(
                $this->em()
            )
        );
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function testRegisterNewUser()
    {
        $savedUser = $this->userService->register('chris', 'holland');
        $retrievedUser = $this->userService->byId($savedUser->getId());

        self::assertTrue($retrievedUser->is($savedUser));
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function testAssignAnyRoleToUser()
    {
        $newUser = $this->userService->register('chris', 'holland');

        $this->userService->assignRoleToUser($newUser, AppRole::passenger());
        $retrievedUser = $this->userService->byId($newUser->getId());

        self::assertTrue($newUser->hasRole(AppRole::passenger()));
        self::assertFalse($newUser->hasRole(AppRole::driver()));

        $this->userService->assignRoleToUser($retrievedUser, AppRole::driver());
        $reRetrievedUser = $this->userService->byId($retrievedUser->getId());

        self::assertTrue($reRetrievedUser->hasRole(AppRole::driver()));
        self::assertTrue($reRetrievedUser->hasRole(AppRole::passenger()));
    }
}
