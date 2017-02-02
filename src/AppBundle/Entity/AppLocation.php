<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="locations")
 */
class AppLocation
{
    /**
     * @var int $id;
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * @var \DateTime $created
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;

    /**
     * @param float $lat
     * @param float $long
     */
    public function __construct($lat, $long)
    {
        $this->lat = $lat;
        $this->long = $long;
        $this->created = new \DateTime();
    }

    public function getLat()
    {
        return $this->lat;
    }

    public function getLong()
    {
        return $this->long;
    }
}
