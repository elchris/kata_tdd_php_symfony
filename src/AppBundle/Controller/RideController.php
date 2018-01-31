<?php


namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;

class RideController extends AppController
{
    /**
     * @Rest\Post("/api/v1/ride")
     * @param Request $request
     * @return \AppBundle\Entity\Ride
     * @throws \AppBundle\Exception\UserNotFoundException
     * @throws \AppBundle\Exception\UserNotInPassengerRoleException
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
}
