<?php


namespace AppBundle\Service;

use AppBundle\Entity\AppLocation;
use AppBundle\Repository\LocationRepositoryInterface;

class LocationService
{
    /**
     * @var LocationRepositoryInterface
     */
    private $locationRepository;

    /**
     * @param LocationRepositoryInterface $locationRepository
     */
    public function __construct(LocationRepositoryInterface $locationRepository)
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
