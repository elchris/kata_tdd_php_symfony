<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class AppRole
 * @package AppBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="roles")
 */
class AppRole
{
    const PASSENGER_ID = 1;
    const PASSENGER_NAME = 'Passenger';
    const DRIVER_ID = 2;
    const DRIVER_NAME = 'Driver';
    /**
     * @ORM\Id()
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id;
    /**
     * @var string
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;

    public function __construct(int $id, string $name)
    {

        $this->id = $id;
        $this->name = $name;
    }

    public static function passenger()
    {
        return new self(self::PASSENGER_ID, self::PASSENGER_NAME);
    }

    public static function driver()
    {
        return new self(self::DRIVER_ID, self::DRIVER_NAME);
    }

    public static function fromName($roleStringToAssign)
    {
        if ($roleStringToAssign === AppRole::PASSENGER_NAME) {
            return self::passenger();
        } elseif ($roleStringToAssign === AppRole::DRIVER_NAME) {
            return self::driver();
        }
    }

    public function getId()
    {
        return $this->id;
    }
}
