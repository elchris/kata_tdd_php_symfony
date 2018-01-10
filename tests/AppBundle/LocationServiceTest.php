<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppLocation;

class LocationServiceTest extends AppTestCase
{
    public function testGetLocation()
    {
        $referenceLocation = new AppLocation(
            self::HOME_LOCATION_LAT,
            self::HOME_LOCATION_LONG
        );

        /** @var AppLocation $retrievedLocation */
        $retrievedLocation = $this->locationService->getLocation(
            $referenceLocation->getLat(),
            $referenceLocation->getLong()
        );

        self::assertTrue($retrievedLocation->isSameAs($referenceLocation));
    }
}
