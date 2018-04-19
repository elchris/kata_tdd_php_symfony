<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * Class Ride
 * @package AppBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="rides")
 */
class Ride
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
     * @var AppUser
     * @ORM\ManyToOne(targetEntity="AppUser", fetch="LAZY")
     * @ORM\JoinColumn(name="passengerId", referencedColumnName="id")
     */
    private $passenger;
    /**
     * @var AppLocation
     * @ORM\ManyToOne(targetEntity="AppLocation", fetch="LAZY")
     * @ORM\JoinColumn(name="departureId", referencedColumnName="id")
     */
    private $departure;

    public function __construct(AppUser $passenger, AppLocation $departure)
    {
        $this->id = Uuid::uuid4();
        $this->passenger = $passenger;
        $this->departure = $departure;
    }

    public function getId()
    {
        return $this->id;
    }

    public function hasPassenger(AppUser $testPassenger)
    {
        return
            $testPassenger->hasAppRole(AppRole::passenger())
            &&
            $testPassenger->is($this->passenger);
    }

    public function hasDeparture(AppLocation $testDeparture)
    {
        return $this->departure->is($testDeparture);
    }
}
