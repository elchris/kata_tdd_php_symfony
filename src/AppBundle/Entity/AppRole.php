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
    const PASSENGER = 'Passenger';
    const DRIVER = 'Driver';
    const PASSENGER_ID = 1;
    const DRIVER_ID = 2;
    /**
     * @var int
     * @ORM\Id()
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
        return new self(self::PASSENGER_ID, self::PASSENGER);
    }

    public static function driver()
    {
        return new self(self::DRIVER_ID, self::DRIVER);
    }

    public static function fromName(string $roleName)
    {
        $role = null;
        switch ($roleName) {
            case self::PASSENGER:
                $role = self::passenger();
                break;
            case self::DRIVER:
                $role = self::driver();
                break;
        }
        return $role;
    }

    public function getId() : int
    {
        return $this->id;
    }
}
