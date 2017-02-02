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

    private function __construct($id, $name)
    {

        $this->id = $id;
        $this->name = $name;
    }

    public static function asPassenger()
    {
        return new self(1, 'Passenger');
    }

    public static function asDriver()
    {
        return new self(2, 'Driver');
    }

    public function getName()
    {
        return $this->name;
    }
}
