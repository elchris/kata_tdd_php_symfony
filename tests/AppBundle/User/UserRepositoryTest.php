<?php


namespace Tests\AppBundle\User;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use Tests\AppBundle\AppTestCase;

class UserRepositoryTest extends AppTestCase
{
    /**
     * @throws \Exception
     */
    public function testCreateNewUser()
    {
        $retrievedUser = $this->getRepoNewUser();

        self::assertTrue($retrievedUser->isNamed('chris holland'));
    }

    /**
     * @throws \Exception
     */
    public function testAssignPassengerRoleToUser()
    {
        $newUserToAssignAsPassenger = $this->getRepoNewUser();
        $newUserToAssignAsPassenger->assignRole(
            $this->userRepository->getRoleReference(AppRole::passenger())
        );

        $this->userRepository->saveUser($newUserToAssignAsPassenger);
        /** @var AppUser $retrievedUser */
        $retrievedUser = $this->userRepository->byId($newUserToAssignAsPassenger->getId());

        self::assertTrue($retrievedUser->hasAppRole(AppRole::passenger()));
        self::assertFalse($retrievedUser->hasAppRole(AppRole::driver()));
    }

    /**
     * @return AppUser
     * @throws \Exception
     */
    protected function getRepoNewUser(): AppUser
    {
        $newUser = new AppUser('chris', 'holland');

        $this->userRepository->saveUser($newUser);
        $this->em()->clear();

        /** @var AppUser $retrievedUser */
        $retrievedUser = $this->userRepository->byId($newUser->getId());

        return $retrievedUser;
    }
}
