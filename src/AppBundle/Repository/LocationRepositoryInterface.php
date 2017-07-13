<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AppLocation;

interface LocationRepositoryInterface
{
    /**
     * @param float $lat
     * @param float $long
     * @return AppLocation
     */
    public function getOrCreateLocation($lat, $long);
}