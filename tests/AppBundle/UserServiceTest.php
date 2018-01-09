<?php

namespace Tests;

use AppBundle\Exception\DuplicateRoleAssignmentException;
use AppBundle\Exception\UserNotFoundException;
use Tests\AppBundle\AppTestCase;

class UserServiceTest extends AppTestCase
{
    public function testRegisterNewUser()
    {
        $user = $this->getSavedUser();
        self::assertSame('chris', $user->getFirstName());
        self::assertSame('holland', $user->getLastName());
    }

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     */
    public function testMakeUserDriver()
    {
        $savedUser = $this->getSavedUser();
        $this->makeUserDriver($savedUser);
        $retrievedUser = $this->getServiceUserById($savedUser->getId());

        self::assertTrue($this->userService->isDriver($retrievedUser));
    }

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     */
    public function testMakeUserPassenger()
    {
        $savedUser = $this->getSavedUser();
        $this->makeUserPassenger($savedUser);
        $retrievedUser = $this->getServiceUserById($savedUser->getId());

        self::assertTrue($this->isPassenger($retrievedUser));
    }
}
