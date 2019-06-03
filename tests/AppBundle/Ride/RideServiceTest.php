<?php

namespace Tests\AppBundle\Ride;

use AppBundle\Entity\Ride;
use AppBundle\Service\RideService;
use Exception;
use Tests\AppBundle\AppTestCase;

class RideServiceTest extends AppTestCase
{
    /**
     * @throws Exception
     */
    public function testCreateRide()
    {
        $rideService = new RideService(
            $this->rideRepository
        );

        $createdRide = $rideService->newRide(
            $this->getRepoPassenger(),
            $this->locationService->getLocation(
                self::HOME_LOCATION_LAT,
                self::HOME_LOCATION_LONG
            )
        );

        /** @var Ride $retrievedRide */
        $retrievedRide = $rideService->byId($createdRide->getId());

        self::assertTrue($retrievedRide->is($createdRide));
    }
}
