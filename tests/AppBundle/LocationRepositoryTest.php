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

    public function setUp()
    {
        parent::setUp();
        $this->locationRepository = new LocationRepository($this->em());
    }

    /** @var  LocationRepository */
    private $locationRepository;

    public function testCreateLocation()
    {
        $homeLocation = $this->getSavedLocation();

        self::assertGreaterThan(0, $homeLocation->getId());
    }

    public function testGetExistingLocationByLatLong()
    {
        $savedLocation = $this->getSavedLocation();
        $lookupLocation = new AppLocation($savedLocation->getLat(), $savedLocation->getLong());

        $retrievedLocation = $this->locationRepository->getLocation($lookupLocation);

        self::assertTrue($retrievedLocation->equals($savedLocation));
    }

    public function testCreateAndGetNewLocation()
    {
        $this->getSavedLocation();
        $workLocation = new AppLocation(37.7721718, -122.4310872);

        $retrievedLocation = $this->locationRepository->getLocation($workLocation);

        self::assertTrue($retrievedLocation->equals($workLocation));
    }

    /**
     * @return AppLocation
     */
    private function getSavedLocation()
    {
        $homeLocation = new AppLocation(
            37.773160,
            -122.432444
        );

        $this->locationRepository->save($homeLocation);

        return $homeLocation;
    }

}
