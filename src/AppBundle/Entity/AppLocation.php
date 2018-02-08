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
     * @ORM\Column(name="longitude", type="float", nullable=false)
     */
    private $long;

    /**
     * @var \DateTime
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;

    /**
     * @var Uuid $id
     * @ORM\Id()
     * @ORM\Column(name="id", type="uuid", unique=true, nullable=false)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
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
        $this->created = new \DateTime();
    }

    public static function cloneFrom(AppLocation $toClone)
    {
        return new self($toClone->lat, $toClone->long);
    }

    public function getLat()
    {
        return $this->lat;
    }

    public function getLong()
    {
        return $this->long;
    }

    public function isSameAs(AppLocation $compareLocation)
    {
        return (
            ($compareLocation->lat === $this->lat)
            &&
            ($compareLocation->long === $this->long)
        );
    }

    public function preDates(AppLocation $compareLocation)
    {
        return $this->created < $compareLocation->created;
    }

    public function equals(AppLocation $compareLocation)
    {
        return $this->id->equals($compareLocation->id);
    }
}
