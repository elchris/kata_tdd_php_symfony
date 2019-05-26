<?php

namespace Tests\AppBundle\User;

use AppBundle\Repository\UserRepository;
use AppBundle\Service\UserSvc;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Tests\AppBundle\AppTestCase;

class UserServiceTest extends AppTestCase
{
    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function testRegisterNewUser()
    {
        $userService = new UserSvc(
            new UserRepository(
                $this->em()
            )
        );

        $savedUser = $userService->register('chris', 'holland');
        $retrievedUser = $userService->byId($savedUser->getId());

        self::assertTrue($retrievedUser->is($savedUser));
    }
}
