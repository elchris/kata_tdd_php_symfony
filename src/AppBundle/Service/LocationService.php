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
     * LocationService constructor.
     * @param LocationRepositoryInterface $locationRepository
     */
    public function __construct(LocationRepositoryInterface $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }

    /**
     * @param $lat
     * @param $long
     * @return AppLocation
     */
    public function getLocation($lat, $long)
    {
        return $this->locationRepository->getLocation(
            new AppLocation(
                $lat,
                $long
            )
        );
    }
}
