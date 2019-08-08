<?php

namespace Tests\AppBundle;

use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEvent;
use AppBundle\Entity\RideEventType;
use AppBundle\Exception\DuplicateRoleAssignmentException;
use AppBundle\Exception\RideNotFoundException;
use AppBundle\Exception\UnauthorizedOperationException;
use AppBundle\Exception\UserNotFoundException;

class RideEventRepositoryTest extends AppTestCase
{
    /** @var Ride $savedRide */
    private $savedRide;

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     * @throws UnauthorizedOperationException
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->savedRide = $this->ride()->getRepoSavedRide();
    }

    /**
     * @throws \Exception
     */
    public function testNonExistentRideThrowsException(): void
    {
        $nonExistentRide = new Ride(
            $this->user()->getSavedUser(),
            $this->location()->getSavedHomeLocation()
        );
        $this->verifyExceptionWithMessage(
            RideNotFoundException::class,
            RideNotFoundException::MESSAGE
        );
        $this->ride()->getRepoLastEvent($nonExistentRide);
    }

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     * @throws UnauthorizedOperationException
     */
    public function testSaveNewRideEvent(): void
    {
        $rideEvent = $this->getSavedRequestedRideEvent();

        self::assertGreaterThan(0, $rideEvent->getId());
    }

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     * @throws UnauthorizedOperationException
     */
    public function testRideIsCurrentlyRequested(): void
    {
        $this->getSavedRequestedRideEvent();

        $lastEventForRide = $this->ride()->getRepoLastEvent($this->savedRide);

        self::assertTrue($lastEventForRide->is(RideEventType::requested()));
    }

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     * @throws UnauthorizedOperationException
     */
    public function testRideIsCurrentlyAccepted(): void
    {
        $this->assertLastEventIsOfType($this->ride()->accepted);
    }

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     * @throws UnauthorizedOperationException
     */
    public function testRideIsCurrentlyInProgress(): void
    {
        $this->assertLastEventIsOfType($this->ride()->inProgress);
    }

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     * @throws UnauthorizedOperationException
     */
    public function testRideIsCurrentlyCancelled(): void
    {
        $this->assertLastEventIsOfType($this->ride()->cancelled);
    }

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     * @throws UnauthorizedOperationException
     */
    public function testRideIsCurrentlyCompleted(): void
    {
        $this->assertLastEventIsOfType($this->ride()->completed);
    }

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     * @throws UnauthorizedOperationException
     */
    public function testRideIsCurrentlyRejected(): void
    {
        $this->assertLastEventIsOfType($this->ride()->rejected);
    }

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     * @throws UnauthorizedOperationException
     */
    public function testMarkRideAsStatus(): void
    {
        $this->ride()->markRepoRide(
            $this->savedRide,
            $this->user()->getSavedPassenger(),
            RideEventType::requested()
        );
        $lastEventForRide = $this->ride()->getRepoLastEvent($this->savedRide);

        self::assertTrue($lastEventForRide->is(RideEventType::requested()));
    }

    /**
     * @return RideEvent
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     * @throws UnauthorizedOperationException
     */
    private function getSavedRequestedRideEvent(): RideEvent
    {
        return $this->ride()->markRepoRide(
            $this->savedRide,
            $this->user()->getSavedPassenger(),
            $this->ride()->requested
        );
    }

    /**
     * @param RideEventType $eventTypeToAssert
     * @return mixed
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     * @throws UnauthorizedOperationException
     */
    private function assertLastEventIsOfType(RideEventType $eventTypeToAssert)
    {
        $this->getSavedRequestedRideEvent();

        $this->save(new RideEvent(
            $this->savedRide,
            $this->user()->getSavedUserWithName('Jamie', 'Isaacs'),
            $eventTypeToAssert
        ));

        $lastEventForRide = $this->ride()->getRepoLastEvent($this->savedRide);

        self::assertFalse($lastEventForRide->is(RideEventType::requested()));
        self::assertTrue($lastEventForRide->is($eventTypeToAssert));

        return $lastEventForRide;
    }
}
