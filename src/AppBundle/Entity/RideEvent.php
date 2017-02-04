<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class RideEvent
 * @package AppBundle\Entity
 * @ORM\Entity()
 * @ORM\Table(name="ride_events")
 */
class RideEvent
{
    /**
     * @var int $id
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;

    /**
     * @var RideEventType
     * @ORM\ManyToOne(targetEntity="RideEventType", fetch="EAGER")
     * @ORM\JoinColumn(name="typeId", referencedColumnName="id")
     */
    private $eventType;
    /**
     * @var Ride
     * @ORM\ManyToOne(targetEntity="Ride", fetch="EAGER")
     * @ORM\JoinColumn(name="rideId", referencedColumnName="id")
     */
    private $ride;
    /**
     * @var AppUser
     * @ORM\ManyToOne(targetEntity="AppUser", fetch="EAGER")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    private $actor;

    /**
     * @param $eventType
     * @param Ride $ride
     * @param AppUser $actor
     */
    public function __construct(RideEventType $eventType, Ride $ride, AppUser $actor)
    {
        $this->eventType = $eventType;
        $this->ride = $ride;
        $this->actor = $actor;
        $this->created = new \DateTime();
    }

    /**
     * @return RideEventType
     */
    public function getType()
    {
        return $this->eventType;
    }

    /**
     * @param RideEventType $eventTypeToCompare
     * @return bool
     */
    public function is(RideEventType $eventTypeToCompare)
    {
        return $this->getType()->equals($eventTypeToCompare);
    }
}
