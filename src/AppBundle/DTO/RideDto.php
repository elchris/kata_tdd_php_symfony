<?php


namespace AppBundle\DTO;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\Ride;

class RideDto
{
    public $id;
    public $driverId;
    /** @var AppLocation  */
    public $destination;
    public $passengerId;

    /**
     * @param Ride $ride
     */
    public function __construct(Ride $ride)
    {
        $this->id = $ride->getId()->toString();
        $this->passengerId = $ride->getPassenger()->getId()->toString();
        if ($ride->hasDriver()) {
            $this->driverId = $ride->getDriver()->getId()->toString();
        }
        if ($ride->hasDestination()) {
            $this->destination = $ride->getDestination();
        }
    }
}
