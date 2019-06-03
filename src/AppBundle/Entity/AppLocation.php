<?php

namespace AppBundle\Entity;

use DateTime;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use Exception;
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
     * @var Uuid $id
     * @ORM\Id()
     * @ORM\Column(name="id", type="uuid", unique=true, nullable=false)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    protected $id;
    /**
     * @var float
     * @ORM\Column(name="latitude", type="float", nullable=false)
     */
    private $lat;
    /**
     * @var float
     * @ORM\Column(name="longitude", type="float", nullable=false)
     */
    private $long;
    /**
     * @var DateTime
     * @ORM\Column(name="created_utc", type="datetime", nullable=false)
     */
    private $createdUTC;


    /**
     * AppLocation constructor.
     * @param float $lat
     * @param float $long
     * @throws Exception
     */
    public function __construct(float $lat, float $long)
    {
        $this->id = Uuid::uuid4();
        $this->lat = $lat;
        $this->long = $long;
        $this->createdUTC = new DateTime(null, new DateTimeZone('UTC'));
    }

    public function getLat() : float
    {
        return $this->lat;
    }

    public function getLong() : float
    {
        return $this->long;
    }

    /**
     * @return AppLocation
     * @throws Exception
     */
    public function clone() : AppLocation
    {
        return new self($this->lat, $this->long);
    }

    public function isSameAs(AppLocation $locationToCompare) : bool
    {
        return (
            ($this->lat === $locationToCompare->lat)
            &&
            ($this->long === $locationToCompare->long)
        );
    }

    public function is(AppLocation $locationToCheck)
    {
        return $locationToCheck->id->equals($this->id);
    }
}
