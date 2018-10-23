<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AppLocation;

interface LocationRepositoryInterface
{
    /**
     * @param AppLocation $lookupLocation
     * @return AppLocation
     */
    public function getLocation(AppLocation $lookupLocation) : AppLocation;
}
