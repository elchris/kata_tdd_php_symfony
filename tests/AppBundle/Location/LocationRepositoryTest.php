<?php

namespace Tests\AppBundle\Location;

use AppBundle\Entity\AppLocation;
use Tests\AppBundle\AppTestCase;

class LocationRepositoryTest extends AppTestCase
{
    public function testGetOrCreateLocation()
    {
        $homeLocation = new AppLocation(
            self::HOME_LOCATION_LAT,
            self::HOME_LOCATION_LONG
        );

        $this->locationRepository->getOrCreateLocation($homeLocation);
        $locationByCoordinates = $this->locationRepository->getOrCreateLocation($homeLocation->clone());

        self::assertTrue($locationByCoordinates->isSameAs($homeLocation));
    }
}
