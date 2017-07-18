<?php


namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @var integer $id
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $firstName
     * @ORM\Column(name="first_name", type="string", nullable=false)
     */
    private $firstName;

    /**
     * @var string $lastName
     * @ORM\Column(name="last_name", type="string", nullable=false)
     */
    private $lastName;

    /**
     * @var ArrayCollection | AppRole[] $roles
     * @ORM\ManyToMany(targetEntity="AppRole")
     * @ORM\JoinTable(
     *     name="users_roles",
     *     joinColumns={@ORM\JoinColumn(name="userId", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="roleId", referencedColumnName="id")}
     * )
     */
    private $roles;

    /**
     * @param $firstName
     * @param $lastName
     */
    public function __construct($firstName, $lastName)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->roles = new ArrayCollection();
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getFullName()
    {
        return $this->firstName.' '.$this->lastName;
    }

    public function addRole(AppRole $role)
    {
        $this->roles->add($role);
    }

    public function hasRole(AppRole $role)
    {
        return $this->roles->contains($role);
    }
}
