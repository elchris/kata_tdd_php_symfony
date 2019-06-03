<?php

namespace AppBundle\Entity;

use AppBundle\Dto\RideDto;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Ramsey\Uuid\Uuid;

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
     * @var Uuid $id
     * @ORM\Id()
     * @ORM\Column(name="id", type="uuid", unique=true, nullable=false)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    protected $id;

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
     * @var DateTime
     * @ORM\Column(name="created_utc", type="datetime", nullable=false)
     */
    private $createdUTC;

    /**
     * Ride constructor.
     * @param AppUser $passenger
     * @param AppLocation $departure
     * @throws Exception
     */
    public function __construct(AppUser $passenger, AppLocation $departure)
    {
        $this->id = Uuid::uuid4();
        $this->passenger = $passenger;
        $this->departure = $departure;
        $this->createdUTC = new DateTime(null, new DateTimeZone('UTC'));
    }

    public function getId() : Uuid
    {
        return $this->id;
    }

    public function is(Ride $rideToCompare) : bool
    {
        return $rideToCompare->id->equals($this->id);
    }

    public function isRiddenBy(AppUser $prospectivePassenger)
    {
        return $prospectivePassenger->is($this->passenger);
    }

    public function isLeavingFrom(AppLocation $depatureToCheck)
    {
        return $depatureToCheck->is($this->departure);
    }

    public function toDto() : RideDto
    {

        $driverId = $this->hasDriver()
                    ? $this->driver->getId()->toString()
                    : null;

        return new RideDto(
            $this->id->toString(),
            $this->passenger->getId()->toString(),
            $this->departure->getLat(),
            $this->departure->getLong(),
            $driverId
        );
    }

    public function assignDriver(AppUser $driver) : void
    {
        $this->driver = $driver;
    }

    public function isDrivenBy(AppUser $driver) : bool
    {
        return $this->driver->is($driver);
    }

    /**
     * @return bool
     */
    private function hasDriver(): bool
    {
        return !is_null($this->driver);
    }

    public function assignDestination(AppLocation $destination)
    {
        $this->destination = $destination;
    }

    public function isDestinedFor(AppLocation $destination)
    {
        return $this->destination->is($destination);
    }
}
