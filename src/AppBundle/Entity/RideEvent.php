<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="rideEvents")
 */
class RideEvent
{
    /**
     * @var integer
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Ride
     * @ORM\ManyToOne(targetEntity="Ride", fetch="EAGER")
     * @ORM\JoinColumn(name="rideId", referencedColumnName="id")
     */
    private $ride;
    /**
     * @var RideEventType
     * @ORM\ManyToOne(targetEntity="RideEventType", fetch="EAGER")
     * @ORM\JoinColumn(name="eventTypeId", referencedColumnName="id")
     */
    private $type;

    /**
     * @var AppUser
     * @ORM\ManyToOne(targetEntity="AppUser", fetch="EAGER")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    private $actor;

    /**
     * @var \DateTime
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;

    /**
     * @param Ride $ride
     * @param RideEventType $type
     * @param AppUser $actor
     */
    public function __construct(
        Ride $ride,
        RideEventType $type,
        AppUser $actor
    )
    {
        $this->ride = $ride;
        $this->type = $type;
        $this->created = new \DateTime();
        $this->actor = $actor;
    }

    /**
     * @return RideEventType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return AppUser
     */
    public function getActor()
    {
        return $this->actor;
    }
}
