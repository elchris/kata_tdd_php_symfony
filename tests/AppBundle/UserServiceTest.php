<?php


namespace AppBundle;

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

    public function testRegisterNewUser()
    {
        $retrievedUser = $this->getSavedNewUser();

        self::assertSame('chris', $retrievedUser->getFirstName());
        self::assertSame('holland', $retrievedUser->getLastName());
    }

    public function testMakeUserDriver()
    {
        $user = $this->getSavedNewUser();

        $this->userService->makeDriver($user);

        self::assertTrue($this->userService->isDriver($user));
    }

    public function testMakeUserPassenger()
    {
        $user = $this->getSavedNewUser();

        $this->userService->makePassenger($user);

        self::assertTrue($this->userService->isPassenger($user));
    }

    /**
     * @return AppUser
     */
    private function getSavedNewUser()
    {
        $this->userService->newUser('chris', 'holland');

        /** @var AppUser $retrievedUser */
        $retrievedUser = $this->userService->getUserById(1);

        return $retrievedUser;
    }
}
