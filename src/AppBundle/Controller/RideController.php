<?php

namespace AppBundle\Controller;

use AppBundle\Dto\RideDto;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;

class RideController extends AppController
{
    /**
     * @Rest\Post("/api/v1/ride")
     * @param Request $request
     * @return RideDto
     * @throws Exception
     */
    public function newRide(Request $request) : RideDto
    {
        $passenger = $this->userService()->byId(
            $this->id(
                $request->get('passenger_id')
            )
        );

        $departure = $this
            ->locationService()
            ->getLocation(
                $request->get('departure_lat'),
                $request->get('departure_long')
            );

        return $this->rideService()->newRide(
            $passenger,
            $departure
        )->toDto();
    }

    /**
     * @Rest\Patch("/api/v1/ride/{rideId}")
     * @param string $rideId
     * @param Request $request
     * @return RideDto
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function patchRide(string $rideId, Request $request) : RideDto
    {
        $rideToPatch = $this->rideService()->byId(
            $this->id($rideId)
        );

        $driverToAssign = $this->userService()->byId(
            $this->id($request->get('driver_id'))
        );

        return $this->rideService()->assignDriverToRide(
            $rideToPatch,
            $driverToAssign
        )->toDto();
    }
}
