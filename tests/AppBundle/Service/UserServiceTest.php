<?php

namespace Tests\AppBundle\Service;

use AppBundle\Repository\UserRepository;
use AppBundle\Service\UserService;
use Tests\AppBundle\AppTestCase;

class UserServiceTest extends AppTestCase
{
    /** @var UserService $userService */
    private $userService;

    public function setUp()
    {
        parent::setUp();
        $this->userService = new UserService(new UserRepository($this->em()));
    }

    public function testMakeNewUser()
    {
        $user = $this->getNewUser();
        self::assertEquals('chris', $user->getFirstName());
        self::assertEquals('holland', $user->getLastName());
    }

    public function testGetSavedUser()
    {
        $user = $this->getNewUser();

        $retrievedUser = $this->userService->getUserById(1);

        self::assertEquals($user->getFirstName(), $retrievedUser->getFirstName());
        self::assertEquals($user->getLastName(), $retrievedUser->getLastName());
    }

    /**
     * @return \AppBundle\Entity\AppUser
     */
    private function getNewUser()
    {
        $user = $this->userService->newUser('chris', 'holland');

        return $user;
    }
}
