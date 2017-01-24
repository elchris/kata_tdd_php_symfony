<?php


namespace AppBundle\Service;

use AppBundle\Exception\AcceptedRideEventLifeCycleException;
use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEvent;
use AppBundle\Repository\RideDao;
use AppBundle\Exception\RideEventLifeCycleException;
use Doctrine\ORM\NoResultException;

class RideService
{
    /**
     * @var RideDao
     */
    private $dao;

    public function __construct(RideDao $dao)
    {

        $this->dao = $dao;
    }

    /**
     * @param AppUser $savedUserOne
     * @param AppLocation $departure
     * @param AppLocation $destination
     */
    public function createRideForUser(
        AppUser $savedUserOne,
        AppLocation $departure,
        AppLocation $destination
    )
    {
        $this->dao->createRideForUser(
            $savedUserOne,
            $departure,
            $destination
        );
    }

    /**
     * @param Ride $ride
     * @param AppUser $actor
     * @throws RideEventLifeCycleException
     */
    public function markRideRequested(Ride $ride, AppUser $actor)
    {
        try {
            $this->getRideStatus($ride);
            throw new RideEventLifeCycleException();
        } catch (NoResultException $e) {
            $this->dao->markRideAsRequested($ride, $actor);
        }
    }

    /**
     * @param Ride $ride
     * @return RideEvent
     */
    public function getRideStatus(Ride $ride)
    {
        return $this->dao->getLastEventForRide($ride);
    }

    /**
     * @param Ride $ride
     * @param AppUser $driver
     * @throws AcceptedRideEventLifeCycleException
     */
    public function markRideAsAcceptedByDriver(Ride $ride, AppUser $driver)
    {
        $this->validateStatusForAccepting($ride);
        $this->assignDriverToRide($ride, $driver);
    }

    /**
     * @param Ride $ride
     * @throws AcceptedRideEventLifeCycleException
     */
    private function validateStatusForAccepting(Ride $ride)
    {
        $lastEvent = $this->getRideStatus($ride);
        if (!$lastEvent->getType()->isRequested()) {
            throw new AcceptedRideEventLifeCycleException(
                'This ride has already been processed: '
                .
                $lastEvent->getType()->getName()
            );
        }
    }

    /**
     * @param Ride $ride
     * @param AppUser $driver
     */
    private function assignDriverToRide(Ride $ride, AppUser $driver)
    {
        $this->dao->makeEventRideAsAcceptedByDriver(
            $ride,
            $driver
        );
        $this->dao->assignDriverToRide(
            $ride,
            $driver
        );
    }
}
