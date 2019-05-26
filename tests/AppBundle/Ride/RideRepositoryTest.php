<?php

namespace Tests\AppBundle\Ride;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
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
        $retrievedRide = $this->getRepoNewRideForPassengerAndDeparture(
            $passenger,
            $departureLocation
        );

        self::assertTrue($retrievedRide->isLeavingFrom($departureLocation));
        self::assertTrue($retrievedRide->isRiddenBy($passenger));
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function testAssignDriverToRide()
    {
        $rideWithPassengerAndDestination = $this->getRepoNewRideForPassengerAndDeparture(
            $this->getRepoNewPassenger(),
            $this->getHomeLocation()
        );

        $newDriver = $this->getRepoNewDriver();
        $rideWithPassengerAndDestination->assignDriver(
            $newDriver
        );
        $this->rideRepository->saveRide($rideWithPassengerAndDestination);
        $retrievedRide = $this->rideRepository->byId($rideWithPassengerAndDestination->getId());

        self::assertTrue($retrievedRide->isDrivenBy($newDriver));
    }

    /**
     * @param AppUser $passenger
     * @param AppLocation $departureLocation
     * @return Ride
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws \Exception
     */
    protected function getRepoNewRideForPassengerAndDeparture(
        AppUser $passenger,
        AppLocation $departureLocation
    ): Ride {
        $newRide = new Ride(
            $passenger,
            $departureLocation
        );
        $this->rideRepository->saveRide($newRide);
        $retrievedRide = $this->rideRepository->byId($newRide->getId());

        return $retrievedRide;
    }
}
