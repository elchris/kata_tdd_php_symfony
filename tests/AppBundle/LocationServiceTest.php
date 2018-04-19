<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppLocation;
use AppBundle\Repository\LocationRepository;
use AppBundle\Service\LocationService;
use Doctrine\ORM\NonUniqueResultException;

class LocationServiceTest extends AppTestCase
{
    /**
     * @throws NonUniqueResultException
     */
    public function testGetOrCreateLocation()
    {
        $locationService = new LocationService(new LocationRepository($this->em()));

        $homeLocationLat = 37.773160;
        $homeLocationLong = -122.432444;

        /** @var AppLocation $createdLocation */
        $createdLocation = $locationService->getOrCreateLocation(
            $homeLocationLat,
            $homeLocationLong
        );

        self::assertSame($homeLocationLat, $createdLocation->getLat());
        self::assertSame($homeLocationLong, $createdLocation->getLong());
    }
}
