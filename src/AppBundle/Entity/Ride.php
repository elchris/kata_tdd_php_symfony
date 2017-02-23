<?php


namespace AppBundle\Entity;

use AppBundle\UnassignedDriverException;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Ride
 * @package AppBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="rides")
 */
class Ride
{
    /**
     * @var int $id
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
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
     * @var \DateTime
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;

    /**
     * @param AppUser $passenger
     * @param AppLocation $departure
     */
    public function __construct(AppUser $passenger, AppLocation $departure)
    {
        $this->passenger = $passenger;
        $this->departure = $departure;
        $this->created = new \DateTime();
    }

    /**
     * @return AppUser
     */
    public function getPassenger()
    {
        return $this->passenger;
    }

    /**
     * @return AppLocation
     */
    public function getDeparture()
    {
        return $this->departure;
    }

    /**
     * @param AppUser $driver
     */
    public function assignDriver(AppUser $driver)
    {
        $this->driver = $driver;
    }

    public function getDriver()
    {
        if (! $this->hasDriver()) {
            throw new UnassignedDriverException();
        }
        return $this->driver;
    }

    /**
     * @return bool
     */
    public function hasDriver()
    {
        return ! is_null($this->driver);
    }

    /**
     * @return AppLocation
     */
    public function getDestination()
    {
        return $this->destination;
    }

    public function assignDestination(AppLocation $destination)
    {
        $this->destination = $destination;
    }
}
