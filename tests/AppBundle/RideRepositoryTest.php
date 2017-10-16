<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Repository\RideRepository;

/**
 * Class RideRepositoryTest
 * @package Tests\AppBundle
 *
 *
 * Ride:
 *          - Departure Location
 *          - Passenger
 *
 *          - Destination
 *          - Driver
 *
 */

class RideRepositoryTest extends AppTestCase
{
    private $destinationWork;
    /** @var  RideRepository */
    private $rideRepository;

    public function setUp()
    {
        parent::setUp();
        $this->rideRepository = new RideRepository($this->em());

        $this->destinationWork = $this->locationService->getLocation(
            self::WORK_LOCATION_LAT,
            self::WORK_LOCATION_LONG
        );
    }

    public function testCreateRideWithDepartureAndPassenger()
    {
        $ride = $this->getSavedRide();

        $this->rideRepository->save($ride);

        self::assertGreaterThan(0, $ride->getId());
    }

    public function testAssignDestinationToRide()
    {
        $retrievedRide = $this->getRideWithDestination();

        self::assertTrue(
            $retrievedRide->getDestination()->equals($this->destinationWork)
        );
    }

    public function testAssignDriverToRide()
    {
        /** @var AppUser $driver */
        $driver = $this->getSavedUserWithName('Jamie', 'Isaacs');

        $rideWithDestination = $this->getRideWithDestination();

        $this->rideRepository->assignDriverToRide($rideWithDestination, $driver);

        $retrievedRide = $this->rideRepository->getRideById($rideWithDestination->getId());

        self::assertSame(
            $driver->getLastName(),
            $retrievedRide->getDriver()->getLastName()
        );
    }

    /**
     * @return Ride
     */
    private function getSavedRide()
    {
        $user = $this->getSavedUser();
        $this->userService->makeUserPassenger($user);

        $passenger = $this->userService->getUserById($user->getId());

        $departure = $this->locationService->getLocation(
            self::HOME_LOCATION_LAT,
            self::HOME_LOCATION_LONG
        );

        $ride = new Ride($passenger, $departure);

        return $ride;
    }

    /**
     * @return Ride
     */
    private function getRideWithDestination()
    {
        $ride = $this->getSavedRide();

        $this->rideRepository->assignDestinationToRide(
            $ride,
            $this->destinationWork
        );

        /** @var Ride $retrievedRide */
        $retrievedRide = $this->rideRepository->getRideById($ride->getId());

        return $retrievedRide;
    }
}
