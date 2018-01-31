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
    const DRIVER = 'Driver';
    const PASSENGER = 'Passenger';
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

    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public static function driver()
    {
        return new self(1, self::DRIVER);
    }

    public static function passenger()
    {
        return new self(2, self::PASSENGER);
    }

    public static function isPassenger($role)
    {
        return $role === self::PASSENGER;
    }

    public static function isDriver($role)
    {
        return $role === self::DRIVER;
    }

    public function getId()
    {
        return $this->id;
    }
}
