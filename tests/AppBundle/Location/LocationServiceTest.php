<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppLocation;
use Tests\AppBundle\Production\LocationApi;

class LocationServiceTest extends AppTestCase
{
    public function testGetLocation()
    {
        $referenceLocation = new AppLocation(
            LocationApi::HOME_LOCATION_LAT,
            LocationApi::HOME_LOCATION_LONG
        );

        /** @var AppLocation $retrievedLocation */
        $retrievedLocation = $this->location()->getLocation(
            $referenceLocation->getLat(),
            $referenceLocation->getLong()
        );

        self::assertTrue($retrievedLocation->isSameAs($referenceLocation));
    }
}
