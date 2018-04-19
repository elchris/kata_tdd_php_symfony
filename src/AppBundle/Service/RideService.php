<?php


namespace AppBundle\Service;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Repository\RideRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Ramsey\Uuid\Uuid;

class RideService
{
    /**
     * @var RideRepository
     */
    private $rideRepository;

    public function __construct(RideRepository $rideRepository)
    {
        $this->rideRepository = $rideRepository;
    }

    public function newRide(AppUser $passenger, AppLocation $departure)
    {
        $newRide = new Ride(
            $passenger,
            $departure
        );
        $this->rideRepository->saveRide($newRide);
        return $newRide;
    }

    /**
     * @param Uuid $id
     * @return Ride
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getById(Uuid $id)
    {
        return $this->rideRepository->getById($id);
    }
}
