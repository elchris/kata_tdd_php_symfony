<?php

namespace Tests;

use Tests\AppBundle\AppTestCase;

class UserServiceTest extends AppTestCase
{
    public function testRegisterNewUser()
    {
        $user = $this->getSavedUser();
        self::assertSame('chris', $user->getFirstName());
        self::assertSame('holland', $user->getLastName());
    }

    public function testMakeUserDriver()
    {
        $savedUser = $this->getSavedUser();
        $this->makeUserDriver($savedUser);
        $retrievedUser = $this->getUserById(1);

        self::assertTrue($this->userService->isDriver($retrievedUser));
    }

    public function testMakeUserPassenger()
    {
        $savedUser = $this->getSavedUser();
        $this->makeUserPassenger($savedUser);
        $retrievedUser = $this->getUserById(1);

        self::assertTrue($this->isPassenger($retrievedUser));
    }
}
