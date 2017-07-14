<?php

namespace AppBundle\Entity;

use AppBundle\Exception\RoleLifeCycleException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class AppUser
 * @ORM\Entity()
 * @ORM\Table(name="users")
 * @package AppBundle\Entity
 */
class AppUser
{
    /**
     * @var integer $id
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

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
     * @var string $firstName
     * @ORM\Column(name="firstName", type="string", nullable=false)
     */
    private $firstName;

    /**
     * @var string $lastName
     * @ORM\Column(name="lastName", type="string", nullable=false)
     */
    private $lastName;

    public function __construct($firstName, $lastName)
    {
        $this->roles = new ArrayCollection();
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }


    public function getId()
    {
        return $this->id;
    }

    public function addRole(AppRole $role)
    {
        if ($this->hasRole($role)) {
            throw new RoleLifeCycleException();
        }
        $this->roles->add($role);
    }

    public function hasRole(AppRole $role)
    {
        return $this->roles->contains($role);
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }
}
