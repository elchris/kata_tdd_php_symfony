<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppUser;
use AppBundle\Entity\AppRole;
use AppBundle\Repository\UserRepository;
use Ramsey\Uuid\Uuid;

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
    public function testCreateAndRetrieveUser()
    {
        $retrievedUser = $this->getSavedUser();
        self::assertTrue($retrievedUser->isNamed('Dan Fritcher'));
    }

    /**
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testAssignPassengerRoleToUser()
    {
        $passengerRole = $this->save(AppRole::passenger());

        $savedUser = $this->getSavedUser();

        $this->userRepository->assignRoleToUser(
            $savedUser,
            AppRole::passenger()
        );

        $retrievedUer = $this->userRepository->getUserById($savedUser->getId());

        self::assertTrue($retrievedUer->hasAppRole(AppRole::passenger()));
    }

    /**
     * @return AppUser
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function getSavedUser()
    {
        $userToSave = new AppUser('Dan', 'Fritcher');
        self::assertNotNull($userToSave->getId());

        $this->userRepository->saveUser($userToSave);

        $retrievedUser = $this->userRepository->getUserById(
            $userToSave->getId()
        );
        self::assertSame($retrievedUser->getId(), $userToSave->getId());

        return $retrievedUser;
    }
}
