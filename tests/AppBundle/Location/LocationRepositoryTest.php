<?php

namespace Tests\AppBundle\Location;

use Exception;
use Tests\AppBundle\AppTestCase;

class LocationRepositoryTest extends AppTestCase
{
    /**
     * @throws Exception
     */
    public function testGetOrCreateLocation()
    {
        $homeLocation = $this->getRepoHomeLocation();
        $locationByCoordinates = $this->locationRepository->getOrCreateLocation($homeLocation->clone());

        self::assertTrue($locationByCoordinates->isSameAs($homeLocation));
    }
}
