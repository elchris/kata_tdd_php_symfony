<?php

namespace Tests\AppBundle\Ride;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
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

        $newRide = $this->getRideWithPassedPassengerAndDeparture($passenger, $departure);
        $retrievedRide = $this->rideRepository->byId($newRide->getId());

        self::assertTrue($retrievedRide->is($newRide));
        self::assertTrue($retrievedRide->isRiddenBy($passenger));
        self::assertTrue($retrievedRide->isLeavingFrom($departure));
    }

    /**
     * @throws Exception
     */
    public function testAssignDriverToRide()
    {
        $ride = $this->getRideWithPassengerAndDeparture();

        $driver = $this->getRepoDriver();
        $ride->assignDriver(
            $driver
        );

        $savedRide = $this->rideRepository->saveRide($ride);

        self::assertTrue($savedRide->isDrivenBy($driver));
    }

    /**
     * @throws Exception
     */
    public function testAssignDestinationToRide()
    {
        $ride = $this->getRideWithPassengerAndDeparture();
        $destination = $this->getRepoWorkLocation();

        $ride->assignDestination($destination);
        $savedRide = $this->rideRepository->saveRide($ride);

        self::assertTrue($savedRide->isDestinedFor($destination));
    }

    /**
     * @param AppUser $passenger
     * @param AppLocation $departure
     * @return Ride
     * @throws Exception
     */
    private function getRideWithPassedPassengerAndDeparture(
        AppUser $passenger,
        AppLocation $departure
    ): Ride {
        $newRide = new Ride(
            $passenger,
            $departure
        );

        return $this->rideRepository->saveRide($newRide);
    }

    /**
     * @return Ride
     * @throws Exception
     */
    private function getRideWithPassengerAndDeparture(): Ride
    {
        $ride = $this->getRideWithPassedPassengerAndDeparture(
            $this->getRepoPassenger(),
            $this->getRepoHomeLocation()
        );

        return $ride;
    }
}
