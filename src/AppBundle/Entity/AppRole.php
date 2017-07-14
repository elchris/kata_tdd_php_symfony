<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class AppRole
 * @package AppBundle\Entity
 * @ORM\Entity()
 * @ORM\Table(name="roles")
 */
class AppRole
{

    /**
     * @var integer $id
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id;

    /**
     * AppRole constructor.
     * @param int $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    public static function driver()
    {
        return new self(1);
    }

    public static function passenger()
    {
        return new self(2);
    }

    public function getId()
    {
        return $this->id;
    }
}
