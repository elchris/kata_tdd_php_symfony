<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppLocation;
use Tests\AppBundle\Production\LocationApi;

class LocationRepositoryTest extends AppTestCase
{
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
        $workLocation = new AppLocation(
            LocationApi::WORK_LOCATION_LAT,
            LocationApi::WORK_LOCATION_LONG
        );

        $retrievedLocation = $this->getOrCreateLocation($workLocation);

        self::assertTrue($retrievedLocation->isSameAs($workLocation));
    }

    /**
     * @return AppLocation
     */
    private function getSavedLocation()
    {
        $homeLocation = new AppLocation(
            LocationApi::HOME_LOCATION_LAT,
            LocationApi::HOME_LOCATION_LONG
        );

        $this->save($homeLocation);

        return $homeLocation;
    }

    protected function getOrCreateLocation(AppLocation $lookupLocation)
    {
        return $this->location()->getRepo()->getLocation($lookupLocation);
    }
}
