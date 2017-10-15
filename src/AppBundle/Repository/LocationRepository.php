<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AppLocation;
use Doctrine\ORM\NoResultException;

class LocationRepository extends AppRepository
{

    /**
     * @param AppLocation $lookupLocation
     * @return AppLocation
     */
    public function getLocation(AppLocation $lookupLocation)
    {
        try {
            return $this
                ->em
                ->createQuery(
                    'SELECT l FROM E:AppLocation l WHERE l.lat = :lat AND l.long = :long'
                )
                ->setParameter('lat', $lookupLocation->getLat())
                ->setParameter('long', $lookupLocation->getLong())
                ->getSingleResult();
        } catch (NoResultException $e) {
            $this->save($lookupLocation);
            return $this->getLocation($lookupLocation);
        }
    }
}
