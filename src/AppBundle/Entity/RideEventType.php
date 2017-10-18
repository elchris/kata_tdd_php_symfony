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

    public function isRequested()
    {
        return $this->id === self::REQUESTED_ID;
    }

    public function isAccepted()
    {
        return $this->id === self::ACCEPTED_ID;
    }
}
