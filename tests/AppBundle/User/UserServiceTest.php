<?php

namespace Tests\AppBundle\User;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;
use AppBundle\Service\UserSvc;
use Tests\AppBundle\AppTestCase;

class UserServiceTest extends AppTestCase
{
    /**
     * @var UserSvc $userService
     */
    private $userService;

    public function setUp()
    {
        parent::setUp();
        $this->userService = new UserSvc(new UserRepository($this->em()));
    }

    public function testRegisterNewUser()
    {
        $rogueUser = new AppUser('rogue', 'user');

        $createdUser = $this->userService->newUser('chris', 'last');
        $retrievedUser = $this->userService->byId($createdUser->getId());

        self::assertTrue($retrievedUser->is($createdUser));
        self::assertFalse($retrievedUser->is($rogueUser));
    }

    public function testAssignRoleToUser()
    {
        $createdUser = $this->userService->newUser('chris', 'last');
        $retrievedUser = $this->userService->byId($createdUser->getId());

        $this->userService->assignRoleToUser($retrievedUser, AppRole::passenger());
        $retrievedUser = $this->userService->byId($createdUser->getId());

        self::assertTrue($retrievedUser->hasAppRole(AppRole::passenger()));
    }
}
