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
     * @var int
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
     * @throws \Exception
     */
    public function __construct(float $lat, float $long)
    {
        $this->id = Uuid::uuid4();
        $this->lat = $lat;
        $this->long = $long;
        $this->created = new \DateTime();
    }

    public function isSameAs(AppLocation $locationToCompare)
    {
        return
            ($locationToCompare->lat === $this->lat)
            &&
            ($locationToCompare->long === $this->long)
            ;
    }

    public function getLat() : float
    {
        return $this->lat;
    }

    public function getLong() : float
    {
        return $this->long;
    }
}
