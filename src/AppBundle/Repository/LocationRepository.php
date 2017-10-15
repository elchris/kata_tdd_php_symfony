<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AppLocation;

class LocationRepository extends AppRepository
{

    /**
     * @param AppLocation $lookupLocation
     * @return AppLocation
     */
    public function getLocation(AppLocation $lookupLocation)
    {
       return $this
               ->em
                ->createQuery(
                    'select l from E:AppLocation l where l.lat = :lat and l.long = :long'
                )
                ->setParameter('lat', $lookupLocation->getLat())
                ->setParameter('long', $lookupLocation->getLong())
                ->getSingleResult();
    }
}
