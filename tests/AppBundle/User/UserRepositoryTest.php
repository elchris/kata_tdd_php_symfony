<?php

namespace Tests\AppBundle\User;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use Exception;
use Tests\AppBundle\AppTestCase;

class UserRepositoryTest extends AppTestCase
{
    /**
     * @throws Exception
     */
    public function testCreateNewUser(): void
    {
        $newUser = $this->getRepoNewUser();
        $this->em()->clear();
        /** @var AppUser $retrievedUser */
        $retrievedUser = $this->userRepository->byId($newUser->getId());

        self::assertTrue($retrievedUser->isNamed('chris holland'));
    }

    /**
     * @throws Exception
     */
    public function testAssignPassengerRoleToUser(): void
    {
        $user = $this->getRepoNewUser();
        $user->assignRole(
            $this->userRepository->getRoleReference(
                AppRole::passenger()
            )
        );

        $this->userRepository->saveAndGet($user);
        $this->em()->clear();
        $retrievedUser = $this->userRepository->byId($user->getId());

        self::assertTrue($retrievedUser->hasRole(AppRole::passenger()));
        self::assertFalse($retrievedUser->hasRole(AppRole::driver()));
    }

    /**
     * @return AppUser
     * @throws Exception
     */
    private function getRepoNewUser(): AppUser
    {
        $newUser = new AppUser('chris', 'holland');
        self::assertNotNull($newUser->getId());

        return $this->userRepository->saveAndGet($newUser);
    }
}
