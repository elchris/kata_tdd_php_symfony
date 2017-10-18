<?php

namespace Tests\AppBundle;

use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEvent;
use AppBundle\Entity\RideEventType;
use AppBundle\Repository\RideEventRepository;

class RideEventRepositoryTest extends AppTestCase
{
    /** @var RideEventType $requestedType */
    private $requestedType;

    /** @var RideEventType $acceptedType */
    private $acceptedType;

    /** @var Ride $savedRide */
    private $savedRide;

    /** @var  RideEventRepository */
    private $rideEventRepository;

    public function setUp()
    {
        parent::setUp();
        $this->rideEventRepository = new RideEventRepository($this->em());

        $this->savedRide = $this->getSavedRide();
        $this->requestedType = RideEventType::requested();
        $this->acceptedType = RideEventType::accepted();
        $this->save($this->requestedType);
        $this->save($this->acceptedType);
    }

    public function testSaveNewRideEvent()
    {
        $rideEvent = $this->getSavedRequestedRideEvent();

        self::assertGreaterThan(0, $rideEvent->getId());
    }

    public function testRideIsCurrentlyRequested()
    {
        $this->getSavedRequestedRideEvent();

        $lastEventForRide = $this->rideEventRepository->getLastEventForRide(
            $this->savedRide
        );

        self::assertTrue($lastEventForRide->isRequested());
    }

    public function testRideIsCurrentlyAccepted()
    {
        $this->getSavedRequestedRideEvent();

        $acceptedEvent = new RideEvent(
            $this->savedRide,
            $this->getSavedUserWithName('Jamie', 'Isaacs'),
            $this->acceptedType
        );

        $this->rideEventRepository->save($acceptedEvent);

        $lastEventForRide = $this->rideEventRepository->getLastEventForRide(
            $this->savedRide
        );

        self::assertFalse($lastEventForRide->isRequested());
        self::assertTrue($lastEventForRide->isAccepted());
    }

    /**
     * @return RideEvent
     */
    private function getSavedRequestedRideEvent()
    {
        $actor = $this->savedRide->getPassenger();

        $rideEvent = new RideEvent(
            $this->savedRide,
            $actor,
            $this->requestedType
        );

        $this->rideEventRepository->save($rideEvent);

        return $rideEvent;
    }
}
