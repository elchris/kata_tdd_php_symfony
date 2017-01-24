<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="roles")
 */
class AppRole
{

    const PASSENGER = 1;
    const DRIVER = 2;

    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id()
     */
    private $role;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;

    /**
     * @param int $role
     */
    public function __construct($role)
    {
        $this->role = $role;
    }

    /**
     * @return AppRole
     */
    public static function asPassenger()
    {
        $role = new self(self::PASSENGER);
        $role->name = 'Passenger';
        return $role;
    }

    /**
     * @return AppRole
     */
    public static function asDriver()
    {
        $role = new self(self::DRIVER);
        $role->name = 'Driver';
        return $role;
    }

    public function getName()
    {
        return $this->name;
    }
}
