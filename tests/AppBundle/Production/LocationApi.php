<?php


namespace Tests\AppBundle\Production;

use AppBundle\Entity\AppLocation;
use AppBundle\Repository\LocationRepository;
use AppBundle\Repository\LocationRepositoryInterface;
use AppBundle\Service\LocationService;
use Doctrine\ORM\EntityManagerInterface;

class LocationApi
{
    const HOME_LOCATION_LAT = 37.773160;
    const HOME_LOCATION_LONG = -122.432444;
    const WORK_LOCATION_LAT = 37.7721718;
    const WORK_LOCATION_LONG = -122.4310872;

    private $locationRepository;
    private $locationService;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->locationRepository = new LocationRepository(
            $entityManager
        );
        $this->locationService = new LocationService(
            $this->locationRepository
        );
    }

    /**
     * @return LocationRepositoryInterface
     */
    public function getRepo()
    {
        return $this->locationRepository;
    }

    /**
     * @return AppLocation
     */
    public function getSavedHomeLocation()
    {
        return $this->locationService->getLocation(
            self::HOME_LOCATION_LAT,
            self::HOME_LOCATION_LONG
        );
    }

    /**
     * @param $lat
     * @param $long
     * @return AppLocation
     */
    public function getLocation($lat, $long)
    {
        return $this->locationService->getLocation(
            $lat,
            $long
        );
    }

    public function getWorkLocation()
    {
        return $this->getLocation(
            self::WORK_LOCATION_LAT,
            self::WORK_LOCATION_LONG
        );
    }

    /**
     * @return LocationService
     */
    public function getService() : LocationService
    {
        return $this->locationService;
    }
}
