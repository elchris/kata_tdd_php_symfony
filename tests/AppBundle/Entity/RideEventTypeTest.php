<?php


namespace Tests\AppBundle\Entity;

use AppBundle\Entity\RideEventType;
use Tests\AppBundle\AppTestCase;

class RideEventTypeTest extends AppTestCase
{
    public function testNewTypeById()
    {
        self::assertTrue(
            RideEventType::requested()->equals(
                RideEventType::newById(RideEventType::REQUESTED_ID)
            )
        );
    }
}
