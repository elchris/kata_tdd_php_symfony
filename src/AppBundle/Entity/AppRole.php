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
    /**
     * @var int $id
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id;
    /**
     * @var string $name
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;

    const PASSENGER = 'Passenger';
    const DRIVER = 'Driver';

    const PASSENGER_ID = 1;
    const DRIVER_ID = 2;

    private function __construct(int $id, string $name)
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

    public static function fromKey(string $roleToPatch)
    {
        $roleDefinitions = [
            self::PASSENGER => self::PASSENGER_ID,
            self::DRIVER => self::DRIVER_ID
        ];
        return new self($roleDefinitions[$roleToPatch], $roleToPatch);
    }

    public function getId()
    {
        return $this->id;
    }
}
