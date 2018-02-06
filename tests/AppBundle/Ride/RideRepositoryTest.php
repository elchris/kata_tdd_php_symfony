<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Exception\DuplicateRoleAssignmentException;
use AppBundle\Exception\RideNotFoundException;
use AppBundle\Exception\UserNotFoundException;

class RideRepositoryTest extends AppTestCase
{
    /**
     * @throws RideNotFoundException
     */
    public function testRideNotFoundThrowsException()
    {
        $nonExistentRide = new Ride(
            $this->user()->getSavedUser(),
            $this->location()->getSavedHomeLocation()
        );

        $this->expectException(RideNotFoundException::class);

        $this->ride()->getRepoRideById($nonExistentRide->getId());
    }

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     */
    public function testCreateRideWithDepartureAndPassenger()
    {
        $ride = $this->ride()->getRepoSavedRide();

        self::assertNotEmpty($ride->getId());
    }

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     */
    public function testAssignDestinationToRide()
    {
        $retrievedRide = $this->ride()->getRepoRideWithDestination();

        self::assertTrue($retrievedRide->isDestinedFor($this->location()->getWorkLocation()));
    }

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     */
    public function testAssignDriverToRide()
    {
        /** @var AppUser $driver */
        $driver = $this->user()->getSavedUserWithName('Jamie', 'Isaacs');
        $rideWithDestination = $this->ride()->getRepoRideWithDestination();

        $this->ride()->assignRepoDriverToRide($rideWithDestination, $driver);
        $retrievedRide = $this->ride()->getRepoRideById($rideWithDestination->getId());

        self::assertTrue($retrievedRide->isDrivenBy($driver));
    }
}
