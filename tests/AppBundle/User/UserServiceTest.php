<?php


namespace Tests\AppBundle\User;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;
use AppBundle\Service\AppUserService;
use Tests\AppBundle\AppTestCase;

class UserServiceTest extends AppTestCase
{

    /** @var AppUserService */
    private $userService;

    public function setUp()
    {
        parent::setUp();
        $this->userService = new AppUserService(new UserRepository($this->em()));
    }

    /**
     * @throws \Exception
     */
    public function testRegisterNewUser()
    {
        $registeredUser = $this->getSvcNewUser();

        /** @var AppUser $retrievedUser */
        $retrievedUser = $this->userService->byId($registeredUser->getId());

        self::assertTrue($retrievedUser->isNamed('chris holland'));
        self::assertTrue($retrievedUser->is($registeredUser));
    }

    /**
     * @throws \Exception
     */
    public function testAssignRoleToUser()
    {
        $newUser = $this->getSvcNewUser();

        $this->userService->assignRoleToUser($newUser, AppRole::passenger());

        $retrievedUser = $this->userService->byId($newUser->getId());
        self::assertTrue($retrievedUser->hasAppRole(AppRole::passenger()));

        $this->userService->assignRoleToUser($retrievedUser, AppRole::driver());
        $reRetrievedUser = $this->userService->byId($retrievedUser->getId());
        self::assertTrue($reRetrievedUser->hasAppRole(AppRole::driver()));
    }

    /**
     * @return AppUser
     * @throws \Exception
     */
    protected function getSvcNewUser(): AppUser
    {
        /** @var AppUser $registeredUser */
        $registeredUser = $this->userService->registerUser('chris', 'holland');
        $this->em()->clear();

        return $registeredUser;
    }
}
