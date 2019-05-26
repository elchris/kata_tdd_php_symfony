<?php

namespace Tests\AppBundle\Ride;

use AppBundle\Entity\Ride;
use AppBundle\Service\RideService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Tests\AppBundle\AppTestCase;

class RideServiceTest extends AppTestCase
{
    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws \Exception
     */
    public function testCreateNewRide()
    {
        $passenger = $this->getRepoNewPassenger();
        $destination = $this->getHomeLocation();

        $rideService = new RideService(
            $this->rideRepository
        );

        /** @var Ride $newRide */
        $newRide = $rideService->newRide(
            $passenger,
            $destination
        );

        /** @var Ride $retrievedRide */
        $retrievedRide = $rideService->byId($newRide->getId());

        self::assertTrue($retrievedRide->isRiddenBy($passenger));
        self::assertTrue($retrievedRide->isDestinedFor($destination));
    }
}
