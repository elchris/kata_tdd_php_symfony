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

        self::assertNotEmpty($homeLocation->getId());
    }

    public function testGetExistingLocationByLatLong()
    {
        $savedLocation = $this->getSavedLocation();
        $lookupLocation = AppLocation::cloneFrom($savedLocation);

        $retrievedLocation = $this->getOrCreateLocation($lookupLocation);

        self::assertTrue($retrievedLocation->equals($savedLocation));
    }

    public function testCreateAndGetNewLocation()
    {
        $this->getSavedLocation();
        $workLocation = new AppLocation(self::WORK_LOCATION_LAT, self::WORK_LOCATION_LONG);

        $retrievedLocation = $this->getOrCreateLocation($workLocation);

        self::assertTrue($retrievedLocation->equals($workLocation));
    }

    /**
     * @return AppLocation
     */
    private function getSavedLocation()
    {
        $homeLocation = new AppLocation(
            self::HOME_LOCATION_LAT,
            self::HOME_LOCATION_LONG
        );

        $this->locationRepository->save($homeLocation);

        return $homeLocation;
    }

    protected function getOrCreateLocation(AppLocation $lookupLocation)
    {
        return $this->locationRepository->getLocation($lookupLocation);
    }
}
