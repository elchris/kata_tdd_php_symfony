<?php

namespace Tests;

use AppBundle\Exception\DuplicateRoleAssignmentException;
use AppBundle\Exception\UserNotFoundException;
use Tests\AppBundle\AppTestCase;

class UserServiceTest extends AppTestCase
{
    public function testRegisterNewUser()
    {
        $user = $this->user()->getSavedUser();
        self::assertTrue($user->isNamed('chris holland'));
    }

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     */
    public function testMakeUserDriver()
    {
        $savedUser = $this->user()->getSavedUser();
        $this->user()->makeUserDriver($savedUser);
        $retrievedUser = $this->user()->getServiceUserById($savedUser->getId());

        self::assertTrue($this->user()->isDriver($retrievedUser));
    }

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     */
    public function testMakeUserPassenger()
    {
        $savedUser = $this->user()->getSavedUser();
        $this->user()->makeUserPassenger($savedUser);
        $retrievedUser = $this->user()->getServiceUserById($savedUser->getId());

        self::assertTrue($this->user()->isPassenger($retrievedUser));
    }
}
