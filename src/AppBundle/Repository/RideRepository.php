<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;

class RideRepository extends AppRepository
{
    public function assignDestinationToRide(Ride $ride, AppLocation $destination)
    {
        $ride->setDestination($destination);
        $this->save($ride);
    }

    /**
     * @param $id
     * @return Ride
     */
    public function getRideById($id)
    {
        return $this->em->createQuery(
            'select r from E:Ride r where r.id = :id'
        )
        ->setParameter('id', $id)
        ->getSingleResult();
    }

    public function assignDriverToRide(Ride $ride, AppUser $driver)
    {
        $ride->setDriver($driver);
        $this->save($ride);
    }
}
