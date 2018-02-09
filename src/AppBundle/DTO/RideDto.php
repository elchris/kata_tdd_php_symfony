<?php


namespace AppBundle\DTO;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppUser;
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
     * @param AppUser $passenger
     * @param AppUser $driver
     * @param AppLocation $destination
     */
    public function __construct(
        Ride $ride,
        AppUser $passenger,
        AppUser $driver = null,
        AppLocation $destination = null
    ) {
        $this->id = $ride->getId()->toString();
        $this->passengerId = $passenger->getId()->toString();
        if ($ride->hasDriver()) {
            $this->driverId = $driver->getId()->toString();
        }
        if ($ride->hasDestination()) {
            $this->destination = $destination;
        }
    }
}
