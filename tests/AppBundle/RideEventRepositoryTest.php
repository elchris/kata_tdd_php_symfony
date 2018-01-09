<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEvent;
use AppBundle\Entity\RideEventType;
use AppBundle\Exception\DuplicateRoleAssignmentException;
use AppBundle\Exception\RideNotFoundException;
use AppBundle\Exception\UserNotFoundException;

class RideEventRepositoryTest extends AppTestCase
{
    /** @var Ride $savedRide */
    private $savedRide;

    /**
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     */
    public function setUp()
    {
        parent::setUp();
        $this->savedRide = $this->getSavedRide();
    }

    /**
     * @throws RideNotFoundException
     */
    public function testNonExistentRideThrowsException()
    {
        $nonExistentRide = new Ride(
            $this->getSavedUser(),
            $this->getSavedHomeLocation()
        );

        $this->expectException(RideNotFoundException::class);
        $this->getLastEvent($nonExistentRide);
    }

    public function testSaveNewRideEvent()
    {
        $rideEvent = $this->getSavedRequestedRideEvent();

        self::assertGreaterThan(0, $rideEvent->getId());
    }

    /**
     * @throws RideNotFoundException
     */
    public function testRideIsCurrentlyRequested()
    {
        $this->getSavedRequestedRideEvent();

        $lastEventForRide = $this->getLastEvent($this->savedRide);

        self::assertTrue($lastEventForRide->is(RideEventType::requested()));
    }

    /**
     * @throws RideNotFoundException
     */
    public function testRideIsCurrentlyAccepted()
    {
        $this->assertLastEventIsOfType($this->acceptedType);
    }

    /**
     * @throws RideNotFoundException
     */
    public function testRideIsCurrentlyInProgress()
    {
        $this->assertLastEventIsOfType($this->inProgressType);
    }

    /**
     * @throws RideNotFoundException
     */
    public function testRideIsCurrentlyCancelled()
    {
        $this->assertLastEventIsOfType($this->cancelledType);
    }

    /**
     * @throws RideNotFoundException
     */
    public function testRideIsCurrentlyCompleted()
    {
        $this->assertLastEventIsOfType($this->completedType);
    }

    /**
     * @throws RideNotFoundException
     */
    public function testRideIsCurrentlyRejected()
    {
        $this->assertLastEventIsOfType($this->rejectedType);
    }

    /**
     * @throws RideNotFoundException
     */
    public function testMarkRideAsStatus()
    {
        $this->markRide(
            $this->savedRide,
            $this->savedPassenger,
            RideEventType::requested()
        );
        $lastEventForRide = $this->getLastEvent($this->savedRide);

        self::assertTrue($lastEventForRide->is(RideEventType::requested()));
    }

    /**
     * @return RideEvent
     */
    private function getSavedRequestedRideEvent()
    {
        return $this->markRide(
            $this->savedRide,
            $this->savedPassenger,
            $this->requestedType
        );
    }

    /**
     * @param RideEventType $eventTypeToAssert
     * @return mixed
     * @throws RideNotFoundException
     */
    private function assertLastEventIsOfType(RideEventType $eventTypeToAssert)
    {
        $this->getSavedRequestedRideEvent();

        $this->rideEventRepository->save(new RideEvent(
            $this->savedRide,
            $this->getSavedUserWithName('Jamie', 'Isaacs'),
            $eventTypeToAssert
        ));

        $lastEventForRide = $this->getLastEvent($this->savedRide);

        self::assertFalse($lastEventForRide->is(RideEventType::requested()));
        self::assertTrue($lastEventForRide->is($eventTypeToAssert));

        return $lastEventForRide;
    }

    /**
     * @param Ride $ride
     * @return mixed
     * @throws RideNotFoundException
     */
    protected function getLastEvent(Ride $ride)
    {
        return $this->rideEventRepository->getLastEventForRide($ride);
    }

    protected function markRide(Ride $ride, AppUser $passenger, RideEventType $status)
    {
        return $this->rideEventRepository->markRideStatusByActor(
            $ride,
            $passenger,
            $status
        );
    }
}
