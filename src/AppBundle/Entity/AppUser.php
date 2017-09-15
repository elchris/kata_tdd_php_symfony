<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class AppUser
 * @package AppBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="users")
 */
class AppUser
{
    /**
     * @var string
     * @ORM\Column(name="first_name", type="string", nullable=false)
     */
    private $firstName;
    /**
     * @var string
     * @ORM\Column(name="last_name", type="string", nullable=false)
     */
    private $lastName;

    /**
     * @var integer $id
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @param string $firstName
     * @param string $lastName
     */
    public function __construct($firstName, $lastName)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function getId()
    {
        return $this->id;
    }
}
