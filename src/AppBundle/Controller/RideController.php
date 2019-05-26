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
     * @throws \Exception
     */
    public function newRide(Request $request) : RideDto
    {
        $passenger = $this->user()->byId(
            $this->id($request->get('passengerId'))
        );

        $departure = $this->location()->getLocation(
            floatval($request->get('departureLat')),
            floatval($request->get('departureLong'))
        );

        return $this->ride()->newRide($passenger, $departure)->toDto();
    }
}
