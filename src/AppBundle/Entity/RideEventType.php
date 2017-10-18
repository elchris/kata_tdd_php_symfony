<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class RideEventType
 * @package AppBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="rideEventTypes")
 */
class RideEventType
{
    const REQUESTED_ID = 1;
    const ACCEPTED_ID = 2;
    const IN_PROGRESS_ID = 3;
    const CANCELLED_ID = 4;
    const COMPLETED_ID = 5;
    const REJECTED_ID = 6;

    /**
     * @var int $id
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @var string $name
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;

    private function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public static function requested()
    {
        return new self(self::REQUESTED_ID, 'Requested');
    }

    public static function accepted()
    {
        return new self(self::ACCEPTED_ID, 'Accepted');
    }

    public static function inProgress()
    {
        return new self(self::IN_PROGRESS_ID, 'In Progress');
    }

    public static function cancelled()
    {
        return new self(self::CANCELLED_ID, 'Cancelled');
    }

    public static function completed()
    {
        return new self(self::COMPLETED_ID, 'Completed');
    }

    public static function rejected()
    {
        return new self(self::REJECTED_ID, 'Rejected');
    }

    public function isRequested()
    {
        return $this->id === self::REQUESTED_ID;
    }

    public function isAccepted()
    {
        return $this->id === self::ACCEPTED_ID;
    }

    public function isInProgress()
    {
        return $this->id === self::IN_PROGRESS_ID;
    }

    public function isCancelled()
    {
        return $this->id === self::CANCELLED_ID;
    }

    public function isCompleted()
    {
        return $this->id === self::COMPLETED_ID;
    }

    public function isRejected()
    {
        return $this->id === self::REJECTED_ID;
    }
}
