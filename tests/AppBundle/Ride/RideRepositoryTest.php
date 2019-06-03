<?php

namespace Tests\AppBundle\Ride;

use AppBundle\Entity\Ride;
use AppBundle\Repository\RideRepository;
use Exception;
use Tests\AppBundle\AppTestCase;

class RideRepositoryTest extends AppTestCase
{
    /**
     * @throws Exception
     */
    public function testCreateRide()
    {
        $passenger = $this->getRepoPassenger();
        $departure = $this->getRepoHomeLocation();

        $newRide = new Ride(
            $passenger,
            $departure
        );

        $this->rideRepository->saveRide($newRide);
        $this->em()->clear();
        $retrievedRide = $this->rideRepository->byId($newRide->getId());

        self::assertTrue($retrievedRide->is($newRide));
        self::assertTrue($retrievedRide->isRiddenBy($passenger));
        self::assertTrue($retrievedRide->isLeavingFrom($departure));
    }
}
