<?php

namespace AppBundle\Controller;

use AppBundle\Dto\RideDto;
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
}
