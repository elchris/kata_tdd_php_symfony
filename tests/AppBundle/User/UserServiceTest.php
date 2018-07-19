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
        /** @var AppUser $createdUser */
        $createdUser = $this->getSvcNewUser();
        $retrievedUser = $this->userService->byId($createdUser->getId());

        self::assertTrue($retrievedUser->is($createdUser));
    }

    public function testAssignRoleToUser()
    {
        $user = $this->getSvcNewUser();

        $this->userService->assignRoleToUser($user, AppRole::passenger());
        $retrievedUser = $this->userService->byId($user->getId());

        self::assertTrue($retrievedUser->hasAppRole(AppRole::passenger()));
        self::assertFalse($retrievedUser->hasAppRole(AppRole::driver()));
    }

    protected function getSvcNewUser()
    {
        $createdUser = $this->userService->newUser('chris', 'holland');

        return $createdUser;
    }
}
