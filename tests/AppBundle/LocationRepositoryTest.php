<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppLocation;
use AppBundle\Repository\LocationRepository;

class LocationRepositoryTest extends AppTestCase
{
    /**
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testGetOrCreateLocation()
    {
        $workLocation = new AppLocation(
            37.772178,
            -122.4310872
        );

        $workLocationClone = new AppLocation(
            37.772178,
            -122.4310872
        );

        $locationRepository = new LocationRepository($this->em());

        $savedLocation = $locationRepository->getOrCreateLocation($workLocation);

        self::assertSame($workLocation->getLat(), $savedLocation->getLat());
        self::assertSame($workLocation->getLong(), $savedLocation->getLong());

        $existingLocation = $locationRepository->getOrCreateLocation($workLocationClone);

        self::assertSame($savedLocation->getId(), $existingLocation->getId());
        self::assertSame($workLocation->getLat(), $existingLocation->getLat());
        self::assertSame($workLocation->getLong(), $existingLocation->getLong());
    }
}
