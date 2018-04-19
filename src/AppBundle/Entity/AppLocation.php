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
     * @var Uuid $id
     * @ORM\Id()
     * @ORM\Column(name="id", type="uuid", unique=true, nullable=false)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

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
     * @param float $lat
     * @param float $long
     */
    public function __construct(float $lat, float $long)
    {
        $this->id = Uuid::uuid4();
        $this->lat = $lat;
        $this->long = $long;
    }

    public function getLat()
    {
        return $this->lat;
    }

    public function getLong()
    {
        return $this->long;
    }

    public function getId()
    {
        return $this->id;
    }

    public function is(AppLocation $testLocation)
    {
        return $testLocation->id->equals($this->id);
    }
}
