<?php


namespace AppBundle\Service;

use AppBundle\Entity\AppLocation;
use AppBundle\Repository\LocationRepository;

class LocationService
{
    /**
     * @var LocationRepository
     */
    private $locationRepository;

    /**
     * @param LocationRepository $locationRepository
     */
    public function __construct(LocationRepository $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }

    /**
     * @param float $lat
     * @param float $long
     * @return AppLocation
     */
    public function getLocation($lat, $long)
    {
        return $this->locationRepository->getOrCreateLocation(
            $lat,
            $long
        );
    }
}
