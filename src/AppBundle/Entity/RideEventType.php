<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name = "rideEventTypes")
 */
class RideEventType
{
    const REQUESTED = 1;
    const ACCEPTED_BY_DRIVER = 2;
    /**
     * @var integer
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="name")
     */
    private $name;

    private function __construct($id, $name)
    {

        $this->id = $id;
        $this->name = $name;
    }

    public static function asRequested()
    {
        return new self(self::REQUESTED, 'Requested');
    }

    public function isRequested()
    {
        return $this->id === self::REQUESTED;
    }

    public static function asAcceptedByDriver()
    {
        return new self(self::ACCEPTED_BY_DRIVER, 'Accepted By Driver');
    }

    public function isAccepted()
    {
        return $this->id === self::ACCEPTED_BY_DRIVER;
    }

    public static function asInProgress()
    {
        return new self(3, 'In Progress');
    }

    public static function asCancelled()
    {
        return new self(4, 'Cancelled');
    }

    public static function asCompleted()
    {
        return new self(5, 'Completed');
    }

    public static function asRejectedByDriver()
    {
        return new self(6, 'Rejected By Driver');
    }

    public static function asRequestTimedOut()
    {
        return new self(7, 'Request Expired');
    }

    public function getName()
    {
        return $this->name;
    }
}
