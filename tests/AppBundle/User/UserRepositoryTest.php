<?php

namespace Tests\AppBundle\User;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Tests\AppBundle\AppTestCase;

class UserRepositoryTest extends AppTestCase
{

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function testCreateNewUser()
    {
        $newUser = $this->getRepoSavedUser();
        /** @var AppUser $retrievedUser */
        $this->em()->clear();
        $retrievedUser = $this->userRepository->byId($newUser->getId());

        self::assertTrue($retrievedUser->isNamed('chris holland'));
    }

    /**
     * @throws Exception
     */
    public function testAssignPassengerRoleToUser()
    {
        //TODO: add roles to migration, or manually to DB table
        $this->save(AppRole::passenger());
        $this->save(AppRole::driver());

        $user = $this->getRepoSavedUser();

        $user->assignRole(
            $this->userRepository->getRoleReference(
                AppRole::passenger()
            )
        );
        $this->userRepository->saveUser($user);

        $this->em()->clear();
        $retrievedUser = $this->userRepository->byId($user->getId());

        self::assertTrue($retrievedUser->hasRole(AppRole::passenger()));
        self::assertFalse($retrievedUser->hasRole(AppRole::driver()));
    }

    /**
     * @return AppUser
     * @throws Exception
     */
    private function getRepoSavedUser(): AppUser
    {
        $newUser = new AppUser('chris', 'holland');
        self::assertNotNull($newUser->getId());

        $savedUser = $this->userRepository->saveUser($newUser);

        return $newUser;
    }
}
