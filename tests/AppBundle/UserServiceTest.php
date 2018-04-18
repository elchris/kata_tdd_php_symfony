<?php


namespace Tests\AppBundle;

use AppBundle\Repository\UserRepository;
use AppBundle\Service\UserService;

class UserServiceTest extends AppTestCase
{
    /**
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testRegisterNewUser()
    {
        $userService = new UserService(new UserRepository($this->em()));

        $createdUser = $userService->newUser('dan', 'fritcher');
        self::assertTrue($createdUser->isNamed('dan fritcher'));

        $retrievedUser = $userService->getById($createdUser->getId());
        self::assertSame($createdUser->getId()->toString(), $retrievedUser->getId()->toString());
    }
}
