<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppLocation;
use AppBundle\Repository\LocationRepository;
use AppBundle\Repository\LocationRepositoryInterface;

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

    /** @var  LocationRepositoryInterface */
    private $locationRepository;

    public function testCreateLocation()
    {
        $homeLocation = $this->getSavedLocation();

        self::assertNotNull($homeLocation);
    }

    public function testGetDupeLocationsReturnsFirst()
    {
        $homeLocation = $this->getSavedLocation();
        $dupeHomeLocation = $this->getSavedLocation();
        self::assertTrue($homeLocation->preDates($dupeHomeLocation));
        self::assertTrue($homeLocation->isSameAs($dupeHomeLocation));
        self::assertFalse($dupeHomeLocation->equals($homeLocation));
        $lookupLocation = AppLocation::cloneFrom($homeLocation);

        $retrievedLocation = $this->getOrCreateLocation($lookupLocation);

        self::assertTrue($homeLocation->isSameAs($retrievedLocation));
        self::assertTrue($dupeHomeLocation->isSameAs($retrievedLocation));
        self::assertTrue($homeLocation->equals($retrievedLocation));
        self::assertFalse($dupeHomeLocation->equals($retrievedLocation));
    }

    public function testGetExistingLocationByLatLong()
    {
        $savedLocation = $this->getSavedLocation();
        $lookupLocation = AppLocation::cloneFrom($savedLocation);

        $retrievedLocation = $this->getOrCreateLocation($lookupLocation);

        self::assertTrue($retrievedLocation->isSameAs($savedLocation));
    }

    public function testCreateAndGetNewLocation()
    {
        $workLocation = new AppLocation(self::WORK_LOCATION_LAT, self::WORK_LOCATION_LONG);

        $retrievedLocation = $this->getOrCreateLocation($workLocation);

        self::assertTrue($retrievedLocation->isSameAs($workLocation));
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

        $this->save($homeLocation);

        return $homeLocation;
    }

    protected function getOrCreateLocation(AppLocation $lookupLocation)
    {
        return $this->locationRepository->getLocation($lookupLocation);
    }
}
