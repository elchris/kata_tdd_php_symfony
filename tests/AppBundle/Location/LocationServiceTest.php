<?php

namespace Tests\AppBundle\Location;

use AppBundle\Entity\AppLocation;
use AppBundle\Repository\LocationRepository;
use AppBundle\Service\LocationService;
use Doctrine\ORM\NonUniqueResultException;
use Tests\AppBundle\AppTestCase;

class LocationServiceTest extends AppTestCase
{
    /**
     * @throws NonUniqueResultException
     */
    public function testGetOrCreateLocation()
    {
        $locationService = new LocationService(
            new LocationRepository($this->em())
        );

        $lookupLocation = new AppLocation(
            self::HOME_LOCATION_LAT,
            self::HOME_LOCATION_LONG
        );

        /** @var AppLocation $retrievedLocation */
        $retrievedLocation = $locationService->getLocation(
            self::HOME_LOCATION_LAT,
            self::HOME_LOCATION_LONG
        );
        self::assertTrue($retrievedLocation->isSameAs($lookupLocation));
    }
}
