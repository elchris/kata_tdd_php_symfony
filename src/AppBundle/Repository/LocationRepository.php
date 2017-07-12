<?php


namespace AppBundle\Repository;

use AppBundle\Entity\AppLocation;
use Doctrine\ORM\NoResultException;

class LocationRepository extends AppRepository
{
    /**
     * @param float $lat
     * @param float $long
     * @return AppLocation
     */
    public function getOrCreateLocation($lat, $long)
    {
        try {
            return $this->getExistingLocation($lat, $long);
        } catch (NoResultException $e) {
            $this->save(new AppLocation($lat, $long));
            return $this->getExistingLocation($lat, $long);
        }
    }

    /**
     * @param float $lat
     * @param float $long
     * @return AppLocation
     */
    private function getExistingLocation($lat, $long)
    {
        $matchingLocation =
            $this->em->createQuery(
                'select l from E:AppLocation l where l.lat = :lat and l.long = :long'
            )
                ->setParameter('lat', $lat)
                ->setParameter('long', $long)
                ->getSingleResult();

        return $matchingLocation;
    }
}
