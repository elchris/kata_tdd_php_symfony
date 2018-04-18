<?php


namespace Tests\AppBundle;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;
use AppBundle\Service\UserService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class UserServiceTest extends AppTestCase
{
    /** @var UserService */
    private $userService;

    public function setUp()
    {
        parent::setUp();
        $this->userService = new UserService(new UserRepository($this->em()));
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function testRegisterNewUser()
    {
        $createdUser = $this->getServiceNewUser();

        $retrievedUser = $this->userService->getById($createdUser->getId());
        self::assertSame($createdUser->getId()->toString(), $retrievedUser->getId()->toString());
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function testAssignPassengerRoleToUser()
    {
        $newUser = $this->getServiceNewUser();

        $this->userService->makeUserPassenger($newUser);
        $retrievedUser = $this->userService->getById($newUser->getId());

        self::assertTrue($retrievedUser->hasAppRole(AppRole::passenger()));
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function testAssignDriverRoleToUser()
    {
        $newUser = $this->getServiceNewUser();

        $this->userService->makeUserDriver($newUser);
        $retrievedUser = $this->userService->getById($newUser->getId());

        self::assertTrue($retrievedUser->hasAppRole(AppRole::driver()));
    }

    /**
     * @return AppUser
     */
    protected function getServiceNewUser(): AppUser
    {
        $createdUser = $this->userService->newUser('dan', 'fritcher');
        self::assertTrue($createdUser->isNamed('dan fritcher'));

        return $createdUser;
    }
}
