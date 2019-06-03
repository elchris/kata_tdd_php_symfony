<?php

namespace AppBundle\Service;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Repository\RideRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Ramsey\Uuid\Uuid;

class RideService
{
    /**
     * @var RideRepository
     */
    private $rideRepository;
    /**
     * @var LocationRepository
     */
    private $locationRepository;

    /**
     * RideService constructor.
     * @param RideRepository $rideRepository
     */
    public function __construct(
        RideRepository $rideRepository
    ) {
        $this->rideRepository = $rideRepository;
    }

    /**
     * @param AppUser $passenger
     * @param AppLocation $departure
     * @return Ride
     * @throws Exception
     */
    public function newRide(
        AppUser $passenger,
        AppLocation $departure
    ) : Ride {
        $newRide = new Ride(
            $passenger,
            $departure
        );

        return $this->rideRepository->saveRide($newRide);
    }

    /**
     * @param Uuid $rideId
     * @return Ride
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function byId(Uuid $rideId) : Ride
    {
        return $this->rideRepository->byId($rideId);
    }

    public function assignDriverToRide(Ride $ride, AppUser $driver) : Ride
    {
        $ride->assignDriver($driver);
        return $this->rideRepository->saveRide($ride);
    }
}
