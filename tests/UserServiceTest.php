<?php

namespace Tests;

use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;
use AppBundle\Service\UserService;
use Tests\AppBundle\AppTestCase;

class UserServiceTest extends AppTestCase
{
    /** @var  UserService */
    private $userService;

    public function setUp()
    {
        parent::setUp();
        $this->userService = new UserService(new UserRepository($this->em()));
    }

    /**
     * register new user
     * get user by id
     * make user driver
     * make user passenger
     */

    public function testRegisterNewUser()
    {
        $user = $this->getSavedUser();

        self::assertSame('chris', $user->getFirstName());
        self::assertSame('holland', $user->getLastName());
    }

    public function testMakeUserDriver()
    {
        $savedUser = $this->getSavedUser();

        $this->userService->makeUserDriver($savedUser);
        $retrievedUser = $this->userService->getUserById(1);

        self::assertTrue($this->userService->isDriver($retrievedUser));
    }

    public function testMakeUserPassenger()
    {
        $savedUser = $this->getSavedUser();

        $this->userService->makeUserPassenger($savedUser);
        $retrievedUser = $this->userService->getUserById(1);

        self::assertTrue($this->userService->isPassenger($retrievedUser));
    }

    /**
     * @return AppUser
     */
    private function getSavedUser()
    {
        $this->userService->newUser('chris', 'holland');

        /** @var AppUser $user */
        $user = $this->userService->getUserById(1);

        return $user;
    }
}
