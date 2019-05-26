<?php

namespace AppBundle\Entity;

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

    public function isDestinedFor(AppLocation $locationToCompare)
    {
        return $this->departureLocation->isSameAs($locationToCompare);
    }

    public function isRiddenBy(AppUser $userToCompare)
    {
        return $this->passenger->is($userToCompare);
    }
}
