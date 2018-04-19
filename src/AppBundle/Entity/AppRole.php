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
     * @var integer $id
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id;
    /**
     * @var string
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;

    const PASSENGER = 'Passenger';
    const DRIVER = 'Driver';

    private function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }


    public static function passenger()
    {
        return new self(1, self::PASSENGER);
    }

    public static function driver()
    {
        return new self(2, self::DRIVER);
    }

    public function getId()
    {
        return $this->id;
    }
}
