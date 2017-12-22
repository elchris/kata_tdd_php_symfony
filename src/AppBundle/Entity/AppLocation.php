<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * Class AppLocation
 * @package AppBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="locations")
 */
class AppLocation
{
    /**
     * @var float
     * @ORM\Column(name="lat", type="float", nullable=false)
     */
    private $lat;
    /**
     * @var float
     * @ORM\Column(name="long", type="float", nullable=false)
     */
    private $long;

    /**
     * @var Uuid $id
     * @ORM\Id()
     * @ORM\Column(name="id", type="guid", nullable=false)
     */
    private $id;

    /**
     * AppLocation constructor.
     * @param float $lat
     * @param float $long
     */
    public function __construct($lat, $long)
    {
        $this->id = Uuid::uuid4();
        $this->lat = $lat;
        $this->long = $long;
    }

    public static function cloneFrom(AppLocation $savedLocation)
    {
        return new self($savedLocation->getLat(), $savedLocation->getLong());
    }

    /**
     * @return Uuid
     */
    public function getId()
    {
        return $this->id;
    }

    public function getLat()
    {
        return $this->lat;
    }

    public function getLong()
    {
        return $this->long;
    }

    public function equals(AppLocation $compareLocation)
    {
        return (
            ($compareLocation->getLat() === $this->getLat())
            &&
            ($compareLocation->getLong() === $this->getLong())
        );
    }
}
