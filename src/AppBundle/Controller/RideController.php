<?php

namespace AppBundle\Controller;

use AppBundle\DTO\RideDto;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;

class RideController extends AppController
{
    /**
     * @Rest\Post("/api/v1/ride")
     * @param Request $request
     * @return RideDto
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function postAction(Request $request) : RideDto
    {
        $userId = $request->get('userId');
        $lat = $request->get('location')['lat'];
        $long = $request->get('location')['long'];

        $passenger = $this->user()->getById($this->id($userId));
        $departure = $this->location()->getOrCreateLocation(
            $lat,
            $long
        );

        $newRide = $this->ride()->newRide(
            $passenger,
            $departure
        );

        return $newRide->toDto();
    }
}
