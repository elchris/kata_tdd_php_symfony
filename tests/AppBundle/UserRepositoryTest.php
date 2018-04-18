<?php


namespace Tests\AppBundle;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;

class UserRepositoryTest extends AppTestCase
{

    /** @var UserRepository */
    private $userRepository;

    public function setUp()
    {
        parent::setUp();
        $this->userRepository = new UserRepository($this->em());
    }

    /**
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testCreateUser()
    {
        $retrievedUser = $this->getRepoNewUser();

        self::assertNotNull($retrievedUser->getId());
        self::assertTrue($retrievedUser->isNamed('chris holland'));
    }

    /**
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testAssignPassengerRoleToUser()
    {
        $newUser = $this->getRepoNewUser();
        $passengerRole = AppRole::passenger();
        $this->save($passengerRole);

        $this->userRepository->assignRoleToUser($newUser, $passengerRole);
        $retrievedUser = $this->userRepository->getById($newUser->getId());

        self::assertTrue($retrievedUser->hasAppRole(AppRole::passenger()));
    }

    /**
     * @return AppUser
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function getRepoNewUser(): AppUser
    {
        $newUser = new AppUser('chris', 'holland');
        self::assertNotNull($newUser->getId());

        $this->userRepository->saveUser($newUser);
        $retrievedUser = $this->userRepository->getById($newUser->getId());

        return $retrievedUser;
    }
}
