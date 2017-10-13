<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppLocation;
use AppBundle\Repository\LocationRepository;

class LocationRepositoryTest extends AppTestCase
{
    /*
         * home: 37.773160, -122.432444
         * work: 37.7721718,-122.4310872
         */

    /** @var  LocationRepository */
    private $locationRepository;

    public function testCreateLocation()
    {
        $this->locationRepository = new LocationRepository($this->em());

        $homeLocation = new AppLocation(
            37.773160,
            -122.432444
        );

        $this->locationRepository->save($homeLocation);

        self::assertGreaterThan(0, $homeLocation->getId());
    }
}
