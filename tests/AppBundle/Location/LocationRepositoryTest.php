<?php

namespace Tests\AppBundle\Location;

use AppBundle\Entity\AppLocation;
use AppBundle\Repository\LocationRepository;
use Tests\AppBundle\AppTestCase;

class LocationRepositoryTest extends AppTestCase
{
    const HOME_LOCATION_LAT = 37.773160;
    const HOME_LOCATION_LONG = -122.432444;

    const WORK_LOCATION_LAT = 37.7721718;
    const WORK_LOCATION_LONG = -122.4310872;

    public function testGetOrCreateLocation()
    {
        $homeLocation = new AppLocation(
            self::HOME_LOCATION_LAT,
            self::HOME_LOCATION_LONG
        );

        $locationRepository = new LocationRepository(
            $this->em()
        );

        $locationRepository->getOrCreateLocation($homeLocation);
        $locationByCoordinates = $locationRepository->getOrCreateLocation($homeLocation->clone());

        self::assertTrue($locationByCoordinates->isSameAs($homeLocation));
    }
}
