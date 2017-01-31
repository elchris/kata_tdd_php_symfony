<?php

namespace Tests\AppBundle;

class AppUserServiceTest extends AppTestCase
{
    public function testRetrieveUser()
    {
        self::assertSame(self::USER_TWO_FIRST_NAME, $this->savedUserTwo->getFirstName());
        self::assertSame(self::USER_TWO_LAST_NAME, $this->savedUserTwo->getLastName());

        self::assertSame(self::USER_ONE_FIRST_NAME, $this->savedUserOne->getFirstName());
        self::assertSame(self::USER_ONE_LAST_NAME, $this->savedUserOne->getLastName());
    }

    public function testMakeUserPassenger()
    {
        $this->makePassenger($this->savedUserOne);
        self::assertTrue($this->isPassenger($this->savedUserOne));
        self::assertFalse($this->isPassenger($this->savedUserTwo));
    }

    public function testMakeUserDriver()
    {
        $this->makeDriver($this->savedUserOne);
        self::assertTrue($this->isDriver($this->savedUserOne));
        self::assertFalse($this->isPassenger($this->savedUserOne));
    }

    public function testUserHasTwoRoles()
    {
        $this->makePassenger($this->savedUserOne);
        $this->makeDriver($this->savedUserOne);
        self::assertTrue($this->isDriver($this->savedUserOne));
        self::assertTrue($this->isPassenger($this->savedUserOne));
    }

    public function testAllUsersHaveTwoRoles()
    {
        $this->makePassenger($this->savedUserOne);
        $this->makeDriver($this->savedUserOne);
        $this->makePassenger($this->savedUserTwo);
        $this->makeDriver($this->savedUserTwo);
        self::assertTrue($this->isDriver($this->savedUserOne));
        self::assertTrue($this->isPassenger($this->savedUserOne));
        self::assertTrue($this->isDriver($this->savedUserTwo));
        self::assertTrue($this->isPassenger($this->savedUserTwo));
    }

    public function testDuplicatePassengerRoleThrowsException()
    {
        $roleName = 'Passenger';
        $this->setUpDuplicateRoleThrowsException($roleName);
        $this->makePassenger($this->savedUserOne);
    }

    public function testDuplicateDriverRoleThrowsException()
    {
        $roleName = 'Driver';
        $this->setUpDuplicateRoleThrowsException($roleName);
        $this->makeDriver($this->savedUserOne);
    }
}
