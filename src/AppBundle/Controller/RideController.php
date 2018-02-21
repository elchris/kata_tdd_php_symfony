<?php


namespace AppBundle\Controller;

use AppBundle\DTO\RideDto;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEventType;
use AppBundle\Exception\ActingDriverIsNotAssignedDriverException;
use AppBundle\Exception\RideLifeCycleException;
use AppBundle\Exception\RideNotFoundException;
use AppBundle\Exception\UnauthorizedOperationException;
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
     * @return RideDto
     * @throws UserNotFoundException
     * @throws UserNotInPassengerRoleException
     * @throws UnauthorizedOperationException
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
        return $this
            ->ride()
            ->newRide($passenger, $departure)
            ->toDto()
            ;
    }

    /**
     * @Rest\Get("/api/v1/ride/{id}")
     * @param string $id
     * @return RideDto
     * @throws RideNotFoundException
     */
    public function idAction(string $id)
    {
        return $this->getRide($id)->toDto();
    }

    /**
     * @Rest\Get("/api/v1/ride/{id}/status")
     * @param string $id
     * @return RideEventType
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
     * @return RideDto
     * @throws RideNotFoundException
     * @throws UserNotFoundException
     * @throws RideLifeCycleException
     * @throws UserNotInDriverRoleException
     * @throws ActingDriverIsNotAssignedDriverException
     * @throws UnauthorizedOperationException
     */
    public function patchAction(string $id, Request $request)
    {
        $rideToPatch = $this->getRide($id);
        $eventId = $request->get('eventId');
        $driverId = $request->get('driverId');
        $destinationLat = $request->get('destinationLat');
        $destinationLong = $request->get('destinationLong');
        $this->rideTransition()->updateRideByEventId(
            $rideToPatch,
            $eventId,
            $driverId
        );
        $this->patchRideDestination($destinationLat, $destinationLong, $rideToPatch);
        return $rideToPatch->toDto();
    }

    /**
     * @param string $id
     * @return Ride
     * @throws RideNotFoundException
     */
    private function getRide(string $id): Ride
    {
        return $this->ride()->getRide($this->id($id));
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
                $this->location()->getLocation(
                    floatval($destinationLat),
                    floatval($destinationLong)
                )
            );
        }
    }
}
