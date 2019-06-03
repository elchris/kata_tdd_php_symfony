<?php

namespace Tests\AppBundle\Ride;

use AppBundle\Entity\Ride;
use Exception;
use Tests\AppBundle\AppTestCase;

class RideServiceTest extends AppTestCase
{
    /**
     * @throws Exception
     */
    public function testCreateRide()
    {
        $createdRide = $this->getServiceRideWithPassengerAndDestination();

        /** @var Ride $retrievedRide */
        $retrievedRide = $this->rideService->byId($createdRide->getId());

        self::assertTrue($retrievedRide->is($createdRide));
    }

    /**
     * @throws Exception
     */
    public function testAssignDriverToRide()
    {
        $ride = $this->getServiceRideWithPassengerAndDestination();
        $driver = $this->getRepoDriver();

        /** @var Ride $patchedRide */
        $patchedRide = $this->rideService->assignDriverToRide($ride, $driver);

        self::assertTrue($patchedRide->isDrivenBy($driver));
    }

    /**
     * @return Ride
     * @throws Exception
     */
    protected function getServiceRideWithPassengerAndDestination(): Ride
    {
        return $this->rideService->newRide(
            $this->getRepoPassenger(),
            $this->locationService->getLocation(
                self::HOME_LOCATION_LAT,
                self::HOME_LOCATION_LONG
            )
        );
    }
}
