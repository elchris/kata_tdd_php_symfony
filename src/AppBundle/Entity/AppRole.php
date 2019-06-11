<?php

namespace AppBundle\Entity;

use AppBundle\Exception\InvalidRoleException;
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
    public const PASSENGER = 'Passenger';
    public const DRIVER = 'Driver';
    public const PASSENGER_ID = 1;
    public const DRIVER_ID = 2;

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

    private function __construct(int $id, string $name)
    {

        $this->id = $id;
        $this->name = $name;
    }

    public static function passenger(): AppRole
    {
        return new self(self::PASSENGER_ID, self::PASSENGER);
    }

    public static function driver(): AppRole
    {
        return new self(self::DRIVER_ID, self::DRIVER);
    }

    /**
     * @param string $name
     * @return AppRole
     * @throws InvalidRoleException
     */
    public static function fromName(string $name): ?AppRole
    {
        switch ($name) {
            case self::PASSENGER:
                return self::passenger();
                break;
            case self::DRIVER:
                return self::driver();
                break;
            default:
                throw new InvalidRoleException($name);
        }
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function getName() : string
    {
        return $this->name;
    }
}
