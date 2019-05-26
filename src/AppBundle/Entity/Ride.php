<?php

namespace AppBundle\Entity;

use AppBundle\DTO\RideDto;
use Doctrine\ORM\Mapping as ORM;
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
    private $departureLocation;

    /**
     * @var \DateTime
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;

    /**
     * @var Uuid $id
     * @ORM\Id()
     * @ORM\Column(name="id", type="uuid", unique=true, nullable=false)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;


    /**
     * Ride constructor.
     * @param AppUser $passenger
     * @param AppLocation $departureLocation
     * @throws \Exception
     */
    public function __construct(AppUser $passenger, AppLocation $departureLocation)
    {
        $this->id = Uuid::uuid4();
        $this->passenger = $passenger;
        $this->departureLocation = $departureLocation;
        $this->created = new \DateTime();
    }

    public function getId() : Uuid
    {
        return $this->id;
    }

    public function is(Ride $rideToTest)
    {
        return $rideToTest->id->equals($this->id);
    }

    public function isLeavingFrom(AppLocation $locationToCompare)
    {
        return $this->departureLocation->isSameAs($locationToCompare);
    }

    public function isRiddenBy(AppUser $userToCompare)
    {
        return $this->passenger->is($userToCompare);
    }

    public function isDrivenBy(AppUser $userToCompare)
    {
        return $this->driver->is($userToCompare);
    }

    public function toDto() : RideDto
    {

        $driverId = ! is_null($this->driver) ? $this->driver->getId()->toString() : null;

        return new RideDto(
            $this->id->toString(),
            $this->passenger->getId()->toString(),
            $this->departureLocation->getLat(),
            $this->departureLocation->getLong(),
            $driverId
        );
    }

    public function assignDriver(AppUser $driver)
    {
        $this->driver = $driver;
    }
}
