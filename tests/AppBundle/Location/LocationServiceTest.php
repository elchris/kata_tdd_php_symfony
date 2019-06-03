<?php

namespace Tests\AppBundle\Location;

use AppBundle\Entity\AppLocation;
use AppBundle\Service\LocationService;
use Exception;
use Tests\AppBundle\AppTestCase;

class LocationServiceTest extends AppTestCase
{
    /**
     * @throws Exception
     */
    public function testGetOrCreateLocation()
    {
        $lookupLocation = new AppLocation(
            self::HOME_LOCATION_LAT,
            self::HOME_LOCATION_LONG
        );

        /** @var AppLocation $retrievedLocation */
        $retrievedLocation = $this->locationService->getLocation(
            self::HOME_LOCATION_LAT,
            self::HOME_LOCATION_LONG
        );

        self::assertTrue($retrievedLocation->isSameAs($lookupLocation));
    }
}
