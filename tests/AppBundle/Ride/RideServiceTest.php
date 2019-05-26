<?php

namespace Tests\AppBundle\Ride;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Location\LocationRepositoryTest;

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
        $retrievedRide = $this->getServiceNewRide($passenger, $destination);

        self::assertTrue($retrievedRide->isRiddenBy($passenger));
        self::assertTrue($retrievedRide->isLeavingFrom($destination));
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function testAssignDriverToRide()
    {
        $driver = $this->getRepoNewDriver();

        $retrievedRide = $this->getSvcNewRideWithPassengerDepartureAndDriver($driver);

        self::assertTrue($retrievedRide->isDrivenBy($driver));
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function testAssignDestinationToRide()
    {
        $ride = $this->getSvcNewRideWithPassengerDepartureAndDriver($this->getRepoNewDriver());

        $this->rideService->assignDestinationToRide($ride, $this->getWorkLocation());
        $retrievedRide = $this->rideService->byId($ride->getId());

        self::assertTrue($retrievedRide->isDestinedFor(
            new AppLocation(
                LocationRepositoryTest::WORK_LOCATION_LAT,
                LocationRepositoryTest::WORK_LOCATION_LONG
            )
        ));
    }

    /**
     * @param AppUser $passenger
     * @param AppLocation $destination
     * @return Ride
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws \Exception
     */
    protected function getServiceNewRide(
        AppUser $passenger,
        AppLocation $destination
    ): Ride {
        /** @var Ride $newRide */
        $newRide = $this->rideService->newRide(
            $passenger,
            $destination
        );

        /** @var Ride $retrievedRide */
        $retrievedRide = $this->rideService->byId($newRide->getId());

        return $retrievedRide;
    }

    /**
     * @param AppUser $driver
     * @return Ride
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    protected function getSvcNewRideWithPassengerDepartureAndDriver(AppUser $driver): Ride
    {
        $ride = $this->getServiceNewRide(
            $this->getRepoNewPassenger(),
            $this->getHomeLocation()
        );

        $this->rideService->assignDriverToRide($ride, $driver);
        $retrievedRide = $this->rideService->byId($ride->getId());

        return $retrievedRide;
    }
}
