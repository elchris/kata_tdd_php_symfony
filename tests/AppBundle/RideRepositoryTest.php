<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use Ramsey\Uuid\Uuid;

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

    public function setUp()
    {
        parent::setUp();

        $this->destinationWork = $this->locationService->getLocation(
            self::WORK_LOCATION_LAT,
            self::WORK_LOCATION_LONG
        );
    }

    public function testCreateRideWithDepartureAndPassenger()
    {
        $ride = $this->getSavedRide();

        self::assertNotEmpty($ride->getId());
    }

    public function testAssignDestinationToRide()
    {
        $retrievedRide = $this->getRideWithDestination();

        self::assertTrue($retrievedRide->isDestinedFor($this->destinationWork));
    }

    public function testAssignDriverToRide()
    {
        /** @var AppUser $driver */
        $driver = $this->getSavedUserWithName('Jamie', 'Isaacs');
        $rideWithDestination = $this->getRideWithDestination();

        $this->rideRepository->assignDriverToRide($rideWithDestination, $driver);
        $retrievedRide = $this->getRideById($rideWithDestination->getId());

        self::assertTrue($retrievedRide->isDrivenBy($driver));
    }

    /**
     * @return Ride
     */
    protected function getRideWithDestination()
    {
        $ride = $this->getSavedRide();

        $this->rideRepository->assignDestinationToRide(
            $ride,
            $this->destinationWork
        );

        /** @var Ride $retrievedRide */
        $retrievedRide = $this->getRideById($ride->getId());

        return $retrievedRide;
    }

    /**
     * @param Uuid $id
     * @return Ride
     */
    protected function getRideById(Uuid $id)
    {
        return $this->rideRepository->getRideById($id);
    }
}
