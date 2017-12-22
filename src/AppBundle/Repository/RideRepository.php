<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use Ramsey\Uuid\Uuid;

class RideRepository extends AppRepository
{
    public function assignDestinationToRide(Ride $ride, AppLocation $destination)
    {
        $ride->assignDestination($destination);
        $this->save($ride);
    }

    /**
     * @param Uuid $id
     * @return Ride
     */
    public function getRideById(Uuid $id)
    {
        return $this->em->createQuery(
            'select r from E:Ride r where r.id = :id'
        )
        ->setParameter('id', $id)
        ->getSingleResult();
    }

    public function assignDriverToRide(Ride $ride, AppUser $driver)
    {
        $ride->assignDriver($driver);
        $this->save($ride);
    }
}
