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
        } catch (NonUniqueResultException $nur) {
            //TODO : possibly log what should be a rare occurrence
            //This would only happen if some rogue process inserts duplicate Locations
            //in the Data Store.
            return ($this->em->createQuery(
                'SELECT l FROM E:AppLocation l WHERE l.lat = :lat AND l.long = :long order by l.created asc'
            )
            ->setParameter('lat', $lookupLocation->getLat())
            ->setParameter('long', $lookupLocation->getLong())
            ->getResult())[0];
        }
    }
}
