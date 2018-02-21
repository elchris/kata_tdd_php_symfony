<?php

namespace Tests;

use AppBundle\Entity\AppRole;
use AppBundle\Exception\DuplicateRoleAssignmentException;
use AppBundle\Exception\UnauthorizedOperationException;
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
     * @throws UnauthorizedOperationException
     */
    public function testRogueUserRoleAssignmentException()
    {
        $rogueUser = $this->user()->getSavedUserWithName('Rogue', 'User');
        $authenticatedUser = $this->user()->getSavedUserWithName('Authenticated', 'User');
        $this->user()->setAuthenticatedUser($authenticatedUser);

        $this->verifyExceptionWithMessage(
            UnauthorizedOperationException::class,
            UnauthorizedOperationException::MESSAGE
        );
        $this->user()->makeUserPassenger($rogueUser);
    }

    /**
     * @throws UserNotFoundException
     * @throws UnauthorizedOperationException
     */
    public function testRogueUserAccessException()
    {
        $rogueUser = $this->user()->getSavedUserWithName('Rogue', 'User');
        $authenticatedUser = $this->user()->getSavedUserWithName('Authenticated', 'User');
        $this->user()->setAuthenticatedUser($authenticatedUser);

        $this->verifyExceptionWithMessage(
            UnauthorizedOperationException::class,
            UnauthorizedOperationException::MESSAGE
        );

        $this->user()->getServiceUserById($rogueUser->getId());
    }

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     * @throws UnauthorizedOperationException
     */
    public function testMakeUserDriver()
    {
        $savedUser = $this->user()->getSavedUser();
        $this->user()->makeUserDriver($savedUser);
        $retrievedUser = $this->user()->getServiceUserById($savedUser->getId());

        self::assertTrue($retrievedUser->userHasRole(AppRole::driver()));
    }

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     * @throws UnauthorizedOperationException
     */
    public function testMakeUserPassenger()
    {
        $savedUser = $this->user()->getSavedUser();
        $this->user()->makeUserPassenger($savedUser);
        $retrievedUser = $this->user()->getServiceUserById($savedUser->getId());

        self::assertTrue($retrievedUser->userHasRole(AppRole::passenger()));
    }
}
