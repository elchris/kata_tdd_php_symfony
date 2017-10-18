<?php

namespace Tests\AppBundle;

use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEvent;
use AppBundle\Entity\RideEventType;
use AppBundle\Repository\RideEventRepository;

class RideEventRepositoryTest extends AppTestCase
{
    /** @var Ride $savedRide */
    private $savedRide;
    /** @var  RideEventRepository */
    private $rideEventRepository;

    public function setUp()
    {
        parent::setUp();
        $this->rideEventRepository = new RideEventRepository($this->em());

        $this->savedRide = $this->getSavedRide();
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

    /**
     * @return RideEvent
     */
    private function getSavedRequestedRideEvent()
    {
        $actor = $this->savedRide->getPassenger();
        $type = RideEventType::requested();
        $this->save($type);

        $rideEvent = new RideEvent(
            $this->savedRide,
            $actor,
            $type
        );

        $this->rideEventRepository->save($rideEvent);

        return $rideEvent;
    }
}
