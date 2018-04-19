<?php


namespace AppBundle\Service;

use AppBundle\Entity\AppLocation;
use AppBundle\Repository\LocationRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class LocationService
{
    /**
     * @var LocationRepository
     */
    private $locationRepository;

    public function __construct(LocationRepository $locationRepository)
    {

        $this->locationRepository = $locationRepository;
    }

    /**
     * @param $homeLocationLat
     * @param $homeLocationLong
     * @return AppLocation
     * @throws NonUniqueResultException
     */
    public function getOrCreateLocation($homeLocationLat, $homeLocationLong)
    {
        return $this->locationRepository->getOrCreateLocation(
            new AppLocation($homeLocationLat, $homeLocationLong)
        );
    }
}
