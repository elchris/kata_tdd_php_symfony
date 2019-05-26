<?php

namespace Tests\AppBundle\Location;

use AppBundle\Entity\AppLocation;
use AppBundle\Repository\LocationRepository;
use Doctrine\ORM\NonUniqueResultException;
use Tests\AppBundle\AppTestCase;

class LocationRepositoryTest extends AppTestCase
{
    const HOME_LOCATION_LAT = 37.773160;
    const HOME_LOCATION_LONG = -122.432444;

    const WORK_LOCATION_LAT = 37.7721718;
    const WORK_LOCATION_LONG = -122.4310872;

    /**
     * @throws NonUniqueResultException
     */
    public function testCreateNewLocation()
    {
        $locationRepo = new LocationRepository($this->em());

        $retrievedLocation = $locationRepo->getOrCreateLocation(
            self::HOME_LOCATION_LAT,
            self::HOME_LOCATION_LONG
        );

        $lookupLocation = new AppLocation(
            self::HOME_LOCATION_LAT,
            self::HOME_LOCATION_LONG
        );

        self::assertTrue($retrievedLocation->isSameAs($lookupLocation));
    }
}
