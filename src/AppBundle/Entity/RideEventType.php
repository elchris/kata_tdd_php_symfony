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

    public function isRequested()
    {
        return $this->id === self::REQUESTED_ID;
    }
}
