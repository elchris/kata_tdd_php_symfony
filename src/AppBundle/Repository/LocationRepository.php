<?php


namespace AppBundle\Repository;

use AppBundle\Entity\AppLocation;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class LocationRepository extends AppRepository
{

    /**
     * @param AppLocation $lookupLocation
     * @return AppLocation
     * @throws NonUniqueResultException
     */
    public function getOrCreateLocation(AppLocation $lookupLocation)
    {
        try {
            $found = $this->em->createQuery(
                'select l from E:AppLocation l where l.lat = :lat and l.long = :long'
            )
                ->setParameter('lat', $lookupLocation->getLat())
                ->setParameter('long', $lookupLocation->getLong())
                ->getSingleResult();
            return $found;
        } catch (NoResultException $exception) {
            $this->save($lookupLocation);
            return $this->getOrCreateLocation($lookupLocation);
        }
    }
}
