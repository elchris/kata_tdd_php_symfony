<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppLocation;
use AppBundle\Repository\LocationRepository;
use AppBundle\Service\LocationService;

class LocationServiceTest extends AppTestCase
{
    public function testGetLocation()
    {
        $locationService = new LocationService(new LocationRepository($this->em()));
        $referenceLocation = new AppLocation(
            self::HOME_LOCATION_LAT,
            self::HOME_LOCATION_LONG
        );

        /** @var AppLocation $retrievedLocation */
        $retrievedLocation= $locationService->getLocation($referenceLocation->getLat(), $referenceLocation->getLong());

        self::assertTrue($retrievedLocation->equals($referenceLocation));
    }
}
