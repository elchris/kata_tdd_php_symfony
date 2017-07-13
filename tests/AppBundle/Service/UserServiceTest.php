<?php


namespace Tests\AppBundle\Service;

use AppBundle\Entity\AppRole;
use AppBundle\RoleLifeCycleException;
use Tests\AppBundle\AppTestCase;

class UserServiceTest extends AppTestCase
{
    public function testCreateAndRetrieveUser()
    {
        self::assertEquals('Scott', $this->userTwo->getFirstName());
        self::assertEquals('Chris', $this->userOne->getFirstName());
    }

    public function testMakeUserPassenger()
    {
        $this->userService->assignRoleToUser($this->userOne, AppRole::asPassenger());
        self::assertTrue($this->userService->isUserPassenger($this->userOne));
    }

    public function testMakeUserDriver()
    {
        $this->userService->assignRoleToUser($this->userOne, AppRole::asDriver());
        self::assertTrue($this->userService->isUserDriver($this->userOne));
    }

    public function testUserHasBothRoles()
    {
        $this->userService->assignRoleToUser($this->userOne, AppRole::asPassenger());
        $this->userService->assignRoleToUser($this->userOne, AppRole::asDriver());
        self::assertTrue($this->userService->isUserPassenger($this->userOne));
        self::assertTrue($this->userService->isUserDriver($this->userOne));
    }

    public function testDuplicateRoleAssignmentThrowsException()
    {
        $this->userService->assignRoleToUser($this->userOne, AppRole::asPassenger());
        self::expectException(RoleLifeCycleException::class);
        $this->userService->assignRoleToUser($this->userOne, AppRole::asPassenger());
    }
}
