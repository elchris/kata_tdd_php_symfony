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
    const REQUESTED = 'Requested';
    const ACCEPTED = 'Accepted';
    const IN_PROGRESS_STATUS = 'In Progress';
    const CANCELLED = 'Cancelled';
    const COMPLETED = 'Completed';
    const REJECTED = 'Rejected';

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
        return new self(self::REQUESTED_ID, self::REQUESTED);
    }

    public static function accepted()
    {
        return new self(self::ACCEPTED_ID, self::ACCEPTED);
    }

    public static function inProgress()
    {
        return new self(self::IN_PROGRESS_ID, self::IN_PROGRESS_STATUS);
    }

    public static function cancelled()
    {
        return new self(self::CANCELLED_ID, self::CANCELLED);
    }

    public static function completed()
    {
        return new self(self::COMPLETED_ID, self::COMPLETED);
    }

    public static function rejected()
    {
        return new self(self::REJECTED_ID, self::REJECTED);
    }

    public static function newById($eventTypeId)
    {
        return new self($eventTypeId, '');
    }

    public function equals(RideEventType $typeToCompare)
    {
        return $this->id === $typeToCompare->getId();
    }

    public function getId()
    {
        return $this->id;
    }
}
