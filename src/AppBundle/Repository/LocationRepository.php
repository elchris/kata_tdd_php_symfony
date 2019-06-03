<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AppLocation;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class LocationRepository extends AppRepository
{
    public function getOrCreateLocation(AppLocation $lookupLocation) : AppLocation
    {
        $lat = $lookupLocation->getLat();
        $long = $lookupLocation->getLong();


        try {
            return $this->em->createQuery(
                'select l from E:AppLocation l where l.lat = :lat and l.long = :long'
            )
                ->setParameter('lat', $lat)
                ->setParameter('long', $long)
                ->getSingleResult();
        } catch (NoResultException $e) {
            $this->saveLocation($lookupLocation);
            return $lookupLocation;
            //return $this->getOrCreateLocation($lookupLocation);
        }
    }

    private function saveLocation(AppLocation $location)
    {

        $this->save($location);
    }
}
