<?php

namespace Tests\AppBundle\Ride;

use AppBundle\Entity\Ride;
use AppBundle\Repository\RideRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Tests\AppBundle\AppTestCase;

class RideRepositoryTest extends AppTestCase
{
    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws \Exception
     */
    public function testCreateNewRide()
    {
        $passenger = $this->getRepoNewPassenger();
        $departureLocation = $this->getHomeLocation();
        $newRide = new Ride(
            $passenger,
            $departureLocation
        );
        $rideRepository = new RideRepository($this->em());

        $rideRepository->saveRide($newRide);
        $retrievedRide = $rideRepository->byId($newRide->getId());

        self::assertTrue($retrievedRide->is($newRide));
        self::assertTrue($retrievedRide->isDestinedFor($departureLocation));
        self::assertTrue($retrievedRide->isRiddenBy($passenger));
    }
}
