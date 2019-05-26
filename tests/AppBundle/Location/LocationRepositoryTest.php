<?php

namespace Tests\AppBundle\Location;

use AppBundle\Entity\AppLocation;
use Doctrine\ORM\NonUniqueResultException;
use Tests\AppBundle\AppTestCase;

class LocationRepositoryTest extends AppTestCase
{
    /**
     * @throws NonUniqueResultException
     */
    public function testCreateNewLocation()
    {
        $retrievedLocation = $this->getHomeLocation();

        $lookupLocation = new AppLocation(
            self::HOME_LOCATION_LAT,
            self::HOME_LOCATION_LONG
        );

        self::assertTrue($retrievedLocation->isSameAs($lookupLocation));
    }
}
