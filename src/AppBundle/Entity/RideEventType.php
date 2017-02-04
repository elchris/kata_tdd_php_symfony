<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class RideEventType
 * @package AppBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="ride_event_types")
 */
class RideEventType
{
    /**
     * @var integer $id
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id;
    /**
     * @var string $name
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;

    /**
     * @param int $id
     * @param string $name
     */
    private function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public static function asRequested()
    {
        return new self(1, 'Requested');
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @param RideEventType $eventType
     * @return bool
     */
    public function equals(RideEventType $eventType)
    {
        return $this->id === $eventType->getId();
    }

    private function getId()
    {
        return $this->id;
    }
}
