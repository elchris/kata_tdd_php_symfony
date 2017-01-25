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
     * @var integer
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
     * @param float $lat
     * @param float $long
     */
    public function __construct($lat, $long)
    {
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

    private function floatsEqual($first, $second)
    {
        return ((abs($first - $second)) < 0.00001);
    }

    public function equals(AppLocation $locationToCompare)
    {
        return
                $this->floatsEqual($locationToCompare->getLat(), $this->getLat())
                &&
                $this->floatsEqual($locationToCompare->getLong(), $this->getLong())
            ;
    }
}
