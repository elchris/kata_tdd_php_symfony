<?php


namespace AppBundle\Controller;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEventType;
use AppBundle\Exception\RideLifeCycleException;
use AppBundle\Exception\RideNotFoundException;
use AppBundle\Exception\UserNotFoundException;
use AppBundle\Exception\UserNotInDriverRoleException;
use AppBundle\Exception\UserNotInPassengerRoleException;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;

class RideController extends AppController
{
    /**
     * @Rest\Post("/api/v1/ride")
     * @param Request $request
     * @return \AppBundle\Entity\Ride
     * @throws UserNotFoundException
     * @throws UserNotInPassengerRoleException
     */
    public function postAction(Request $request)
    {
        $passenger = $this->getUserById(
            $request->get('passengerId')
        );
        $departure = $this->location()->getLocation(
            $request->get('departureLat'),
            $request->get('departureLong')
        );
        return $this->ride()->newRide($passenger, $departure);
    }

    /**
     * @Rest\Get("/api/v1/ride/{id}")
     * @param string $id
     * @return Ride
     * @throws RideNotFoundException
     */
    public function idAction(string $id)
    {
        return $this->getRide($id);
    }

    /**
     * @Rest\Get("/api/v1/ride/{id}/status")
     * @param string $id
     * @return \AppBundle\Entity\RideEventType
     * @throws RideNotFoundException
     */
    public function statusAction(string $id)
    {
        return $this->ride()->getRideStatus(
            $this->getRide($id)
        );
    }

    /**
     * @Rest\Patch("/api/v1/ride/{id}")
     * @param string $id
     * @param Request $request
     * @return Ride
     * @throws RideNotFoundException
     * @throws UserNotFoundException
     * @throws RideLifeCycleException
     * @throws UserNotInDriverRoleException
     */
    public function patchAction(string $id, Request $request)
    {
        $rideToPatch = $this->getRide($id);
        $eventId = $request->get('eventId');
        $destinationLat = $request->get('destinationLat');
        $destinationLong = $request->get('destinationLong');
        $this->patchRideLifeCycle($request, $eventId, $rideToPatch);
        $this->patchRideDestination($destinationLat, $destinationLong, $rideToPatch);
        return $rideToPatch;
    }

    /**
     * @param string $id
     * @return Ride
     * @throws RideNotFoundException
     */
    protected function getRide(string $id): Ride
    {
        return $this->ride()->getRide($this->id($id));
    }

    /**
     * @param RideEventType $eventToProcess
     * @param Ride $rideToPatch
     * @param Request $request
     * @throws RideLifeCycleException
     * @throws RideNotFoundException
     * @throws UserNotInDriverRoleException
     * @throws UserNotFoundException
     */
    private function patchRideAcceptance(RideEventType $eventToProcess, Ride $rideToPatch, Request $request): void
    {
        if (RideEventType::accepted()->equals($eventToProcess)) {
            $driver = $this->getUserById($request->get('driverId'));
            $this->ride()->acceptRide($rideToPatch, $driver);
        }
    }

    /**
     * @param Request $request
     * @param $eventId
     * @param Ride $rideToPatch
     * @throws RideLifeCycleException
     * @throws RideNotFoundException
     * @throws UserNotFoundException
     * @throws UserNotInDriverRoleException
     */
    private function patchRideLifeCycle(Request $request, $eventId, Ride $rideToPatch): void
    {
        if (!is_null($eventId)) {
            $eventToProcess = RideEventType::newById(intval($eventId));
            $this->patchRideAcceptance($eventToProcess, $rideToPatch, $request);
        }
    }

    /**
     * @param $destinationLat
     * @param $destinationLong
     * @param Ride $rideToPatch
     */
    private function patchRideDestination($destinationLat, $destinationLong, Ride $rideToPatch): void
    {
        if (!is_null($destinationLat) && !is_null($destinationLong)) {
            $this->ride()->assignDestinationToRide(
                $rideToPatch,
                new AppLocation(
                    floatval($destinationLat),
                    floatval($destinationLong)
                )
            );
        }
    }
}
