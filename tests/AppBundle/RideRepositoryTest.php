<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Exception\DuplicateRoleAssignmentException;
use AppBundle\Exception\RideNotFoundException;
use AppBundle\Exception\UserNotFoundException;
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

    /**
     * @throws RideNotFoundException
     */
    public function testRideNotFoundThrowsException()
    {
        $nonExistentRide = new Ride(
            $this->getSavedUser(),
            $this->getSavedHomeLocation()
        );

        $this->expectException(RideNotFoundException::class);

        $this->getRideById($nonExistentRide->getId());
    }

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     */
    public function testCreateRideWithDepartureAndPassenger()
    {
        $ride = $this->getSavedRide();

        self::assertNotEmpty($ride->getId());
    }

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws RideNotFoundException
     * @throws UserNotFoundException
     */
    public function testAssignDestinationToRide()
    {
        $retrievedRide = $this->getRideWithDestination();

        self::assertTrue($retrievedRide->isDestinedFor($this->destinationWork));
    }

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws RideNotFoundException
     * @throws UserNotFoundException
     */
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
     * @throws DuplicateRoleAssignmentException
     * @throws RideNotFoundException
     * @throws UserNotFoundException
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
     * @return mixed
     * @throws RideNotFoundException
     */
    protected function getRideById(Uuid $id)
    {
        return $this->rideRepository->getRideById($id);
    }
}
