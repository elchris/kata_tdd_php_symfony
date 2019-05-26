<?php

namespace Tests\AppBundle\Ride;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Location\LocationRepositoryTest;

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
        $newDriver = $this->getRepoNewDriver();

        $retrievedRide = $this->getRepoNewRideWithPassengerDepartureAndDriver($newDriver);

        self::assertTrue($retrievedRide->isDrivenBy($newDriver));
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function testAssignDestinationToRide()
    {
        $ride = $this->getRepoNewRideWithPassengerDepartureAndDriver($this->getRepoNewDriver());

        $ride->assignDestination(
            $this->getWorkLocation()
        );

        $this->rideRepository->saveRide($ride);
        $retrievedRide = $this->rideRepository->byId($ride->getId());

        self::assertTrue($retrievedRide->isDestinedFor(
            new AppLocation(
                LocationRepositoryTest::WORK_LOCATION_LAT,
                LocationRepositoryTest::WORK_LOCATION_LONG
            )
        ));
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

    /**
     * @param AppUser $newDriver
     * @return Ride
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    protected function getRepoNewRideWithPassengerDepartureAndDriver(AppUser $newDriver): Ride
    {
        $rideWithPassengerAndDeparture = $this->getRepoNewRideForPassengerAndDeparture(
            $this->getRepoNewPassenger(),
            $this->getHomeLocation()
        );

        $rideWithPassengerAndDeparture->assignDriver(
            $newDriver
        );
        $this->rideRepository->saveRide($rideWithPassengerAndDeparture);
        $retrievedRide = $this->rideRepository->byId($rideWithPassengerAndDeparture->getId());

        return $retrievedRide;
    }
}
