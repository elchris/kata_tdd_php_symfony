<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Ride
 * @package Tests\AppBundle
 *
 * @ORM\Entity()
 * @ORM\Table(name="rides")
 */
class Ride
{
    /**
     * @var AppUser
     * @ORM\ManyToOne(targetEntity="AppUser", fetch="EAGER")
     * @ORM\JoinColumn(name="passengerId", referencedColumnName="id")
     */
    private $passenger;

    /**
     * @var AppUser
     * @ORM\ManyToOne(targetEntity="AppUser", fetch="EAGER")
     * @ORM\JoinColumn(name="driverId", referencedColumnName="id")
     */
    private $driver;

    /**
     * @var AppLocation
     * @ORM\ManyToOne(targetEntity="AppLocation", fetch="EAGER")
     * @ORM\JoinColumn(name="departureId", referencedColumnName="id")
     */
    private $departure;

    /**
     * @var AppLocation
     * @ORM\ManyToOne(targetEntity="AppLocation", fetch="EAGER")
     * @ORM\JoinColumn(name="destinationId", referencedColumnName="id")
     */
    private $destination;

    /**
     * @var int $id
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id;

    /**
     * Ride constructor.
     * @param AppUser $passenger
     * @param AppLocation $departure
     */
    public function __construct(AppUser $passenger, AppLocation $departure)
    {
        $this->passenger = $passenger;
        $this->departure = $departure;
    }

    public function getId()
    {
        return $this->id;
    }

    public function assignDestination(AppLocation $destination)
    {
        $this->destination = $destination;
    }

    public function assignDriver(AppUser $driver)
    {
        $this->driver = $driver;
    }

    public function isDrivenBy(AppUser $driver)
    {
        return $this->driver->is($driver);
    }

    public function isDestinedFor(AppLocation $destinationLocation)
    {
        return $this->destination->equals($destinationLocation);
    }

    public function getPassengerTransaction(RideEventType $status)
    {
        return new RideEvent(
            $this,
            $this->passenger,
            $status
        );
    }
}
