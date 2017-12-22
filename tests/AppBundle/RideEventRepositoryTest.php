<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEvent;
use AppBundle\Entity\RideEventType;

class RideEventRepositoryTest extends AppTestCase
{
    /** @var RideEventType $requestedType */
    private $requestedType;

    /** @var RideEventType $acceptedType */
    private $acceptedType;

    /** @var RideEventType $inProgressType */
    private $inProgressType;

    /** @var RideEventType $cancelledType */
    private $cancelledType;

    /** @var RideEventType $completedType */
    private $completedType;

    /** @var RideEventType $rejectedType */
    private $rejectedType;

    /** @var Ride $savedRide */
    private $savedRide;

    public function setUp()
    {
        parent::setUp();

        $this->savedRide = $this->getSavedRide();
        $this->requestedType = RideEventType::requested();
        $this->acceptedType = RideEventType::accepted();
        $this->inProgressType = RideEventType::inProgress();
        $this->cancelledType = RideEventType::cancelled();
        $this->completedType = RideEventType::completed();
        $this->rejectedType = RideEventType::rejected();
        $this->save($this->requestedType);
        $this->save($this->acceptedType);
        $this->save($this->inProgressType);
        $this->save($this->cancelledType);
        $this->save($this->completedType);
        $this->save($this->rejectedType);
    }

    public function testSaveNewRideEvent()
    {
        $rideEvent = $this->getSavedRequestedRideEvent();

        self::assertGreaterThan(0, $rideEvent->getId());
    }

    public function testRideIsCurrentlyRequested()
    {
        $this->getSavedRequestedRideEvent();

        $lastEventForRide = $this->getLastEvent($this->savedRide);

        self::assertTrue($lastEventForRide->is(RideEventType::requested()));
    }

    public function testRideIsCurrentlyAccepted()
    {
        $this->assertLastEventIsOfType($this->acceptedType);
    }

    public function testRideIsCurrentlyInProgress()
    {
        $this->assertLastEventIsOfType($this->inProgressType);
    }

    public function testRideIsCurrentlyCancelled()
    {
        $this->assertLastEventIsOfType($this->cancelledType);
    }

    public function testRideIsCurrentlyCompleted()
    {
        $this->assertLastEventIsOfType($this->completedType);
    }

    public function testRideIsCurrentlyRejected()
    {
        $this->assertLastEventIsOfType($this->rejectedType);
    }

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
     * @return RideEvent
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
