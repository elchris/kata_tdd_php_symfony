<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Exception\RideNotFoundException;
use Ramsey\Uuid\Uuid;

class RideRepository extends AppRepository implements RideRepositoryInterface
{
    public function assignDestinationToRide(Ride $ride, AppLocation $destination)
    {
        $ride->assignDestination($destination);
        $this->save($ride);
    }

    /**
     * @param Uuid $id
     * @return mixed
     * @throws RideNotFoundException
     */
    public function getRideById(Uuid $id)
    {
        try {
            return $this->em->createQuery(
                'select r from E:Ride r where r.id = :id'
            )
                ->setParameter('id', $id)
                ->getSingleResult();
        } catch (\Exception $e) {
            throw new RideNotFoundException();
        }
    }

    public function assignDriverToRide(Ride $ride, AppUser $driver)
    {
        $ride->assignDriver($driver);
        $this->save($ride);
    }
}
