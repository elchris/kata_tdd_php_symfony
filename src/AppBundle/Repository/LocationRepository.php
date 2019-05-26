<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AppLocation;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class LocationRepository extends AppRepository
{
    /**
     * @param float $lat
     * @param float $long
     * @return AppLocation
     * @throws NonUniqueResultException
     * @throws \Exception
     */
    public function getOrCreateLocation(float $lat, float $long) : AppLocation
    {
        try {
            return $this->em->createQuery(
                'select l from E:AppLocation l where l.lat = :lat and l.long = :long order by l.created desc'
            )
                ->setParameter('lat', $lat)
                ->setParameter('long', $long)
                ->getSingleResult();
        } catch (NoResultException $noResultException) {
            $newLocation = new AppLocation($lat, $long);
            $this->saveLocation($newLocation);
            return $this->getOrCreateLocation($lat, $long);
        }
    }

    private function saveLocation(AppLocation $locationToSave)
    {
        $this->save($locationToSave);
    }
}
