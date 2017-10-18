<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class RideEvent
 * @package AppBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="rideEvents")
 */
class RideEvent
{
    /**
     * @var Ride $ride
     * @ORM\ManyToOne(targetEntity="Ride", fetch="EAGER")
     * @ORM\JoinColumn(name="rideId", referencedColumnName="id")
     */
    private $ride;

    /**
     * @var AppUser $actor
     * @ORM\ManyToOne(targetEntity="AppUser", fetch="EAGER")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    private $actor;

    /**
     * @var RideEventType $type
     * @ORM\ManyToOne(targetEntity="RideEventType", fetch="EAGER")
     * @ORM\JoinColumn(name="eventTypeId", referencedColumnName="id")
     */
    private $type;

    /**
     * @var \DateTime $created
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;

    /**
     * @var int $id
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id;

    /**
     * RideEvent constructor.
     * @param Ride $ride
     * @param AppUser $actor
     * @param RideEventType $type
     */
    public function __construct(Ride $ride, AppUser $actor, RideEventType $type)
    {
        $this->ride = $ride;
        $this->actor = $actor;
        $this->type = $type;
        $this->created = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function isRequested()
    {
        return $this->type->isRequested();
    }

    public function isAccepted()
    {
        return $this->type->isAccepted();
    }

    public function inProgress()
    {
        return $this->type->isInProgress();
    }

    public function isCancelled()
    {
        return $this->type->isCancelled();
    }

    public function isCompleted()
    {
        return $this->type->isCompleted();
    }

    public function isRejected()
    {
        return $this->type->isRejected();
    }
}