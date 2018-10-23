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
     * @throws \Exception
     */
    public function postAction(Request $request): RideDto
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
     * @Rest\Get("/api/v1/ride/{rideId}")
     * @param string $rideId
     * @return RideDto
     * @throws RideNotFoundException
     */
    public function idAction(string $rideId): RideDto
    {
        return $this->getRide($rideId)->toDto();
    }

    /**
     * @Rest\Get("/api/v1/ride/{rideId}/status")
     * @param string $rideId
     * @return RideEventType
     * @throws RideNotFoundException
     */
    public function statusAction(string $rideId): RideEventType
    {
        return $this->ride()->getRideStatus(
            $this->getRide($rideId)
        );
    }

    /**
     * @Rest\Patch("/api/v1/ride/{rideId}")
     * @param string $rideId
     * @param Request $request
     * @return RideDto
     * @throws RideNotFoundException
     * @throws UserNotFoundException
     * @throws RideLifeCycleException
     * @throws UserNotInDriverRoleException
     * @throws ActingDriverIsNotAssignedDriverException
     * @throws UnauthorizedOperationException
     * @throws \Exception
     */
    public function patchAction(string $rideId, Request $request): RideDto
    {
        $rideToPatch = $this->getRide($rideId);
        $eventId = $request->get('eventId');
        $driverId = $request->get('driverId');
        $destinationLat = $request->get('destinationLat');
        $destinationLong = $request->get('destinationLong');
        $this->rideTransition()->updateRideByDriverAndEventId(
            $rideToPatch,
            $eventId,
            $driverId
        );
        $this->patchRideDestination($destinationLat, $destinationLong, $rideToPatch);
        return $rideToPatch->toDto();
    }

    /**
     * @param string $rideId
     * @return Ride
     * @throws RideNotFoundException
     */
    private function getRide(string $rideId): Ride
    {
        return $this->ride()->getRide($this->id($rideId));
    }

    /**
     * @param $destinationLat
     * @param $destinationLong
     * @param Ride $rideToPatch
     * @throws \Exception
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
