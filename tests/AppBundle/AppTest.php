<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppUser;

/**
 * Consult ride-hailing.svg to digest some key application concepts.
 *
 * Consult Kata-Tasks.rtf to get an idea of the various tests you'll be writing
 * and help shape your sequencing.
 *
 * With this said, you do not have to follow the sequencing outlined.
 *
 *
 * Class AppTest
 * @package Tests\AppBundle
 */
class AppTest extends AppTestCase
{
    public function testCreateAndRetrieveUser()
    {
        $this->appService->newUser('Chris', 'Holland');
        $this->appService->newUser('Scott', 'Sims');

        /** @var AppUser $user */
        $user = $this->appService->getUserById(2);
        self::assertEquals('Scott', $user->getFirstName());

        $user = $this->appService->getUserById(1);
        self::assertEquals('Chris', $user->getFirstName());
    }
}
