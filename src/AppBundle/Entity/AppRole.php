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
     * @var string $roleName
     * @ORM\Column(name="role_name", type="string", nullable=false)
     */
    private $roleName;

    private function __construct($roleId, $roleName)
    {
        $this->id = $roleId;
        $this->roleName = $roleName;
    }

    public static function driver()
    {
        return new self(1, 'Driver');
    }

    public static function passenger()
    {
        return new self(2, 'Passenger');
    }

    public function getId()
    {
        return $this->id;
    }
}
