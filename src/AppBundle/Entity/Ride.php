<?php


namespace AppBundle\Entity;

use AppBundle\Exception\NoDriverAssignedException;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="rides")
 */
class Ride
{
    /**
     * @var integer
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
     * @ORM\JoinColumn(name="driverId", referencedColumnName="id", nullable=TRUE)
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
     * @param AppUser $savedUserOne
     * @param AppLocation $departure
     * @param AppLocation $destination
     */
    public function __construct(AppUser $savedUserOne, AppLocation $departure, AppLocation $destination)
    {
        $this->passenger = $savedUserOne;
        $this->departure = $departure;
        $this->destination = $destination;
    }

    /**
     * @return AppLocation
     */
    public function getDeparture()
    {
        return $this->departure;
    }

    /**
     * @return AppLocation
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @return AppUser
     * @throws NoDriverAssignedException
     */
    public function getDriver()
    {
        if (is_null($this->driver)) {
            throw new NoDriverAssignedException();
        }
        return $this->driver;
    }

    public function assignDriver(AppUser $driver)
    {
        $this->driver = $driver;
    }
}
