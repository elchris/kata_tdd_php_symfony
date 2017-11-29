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
     * LocationService constructor.
     * @param LocationRepository $locationRepository
     */
    public function __construct($locationRepository)
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
