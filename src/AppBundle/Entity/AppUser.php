<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * Class AppUser
 * @package AppBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="users")
 */
class AppUser //extends BaseUser
{
    /**
     * @var Uuid $id
     * @ORM\Id()
     * @ORM\Column(name="id", type="uuid", unique=true, nullable=false)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;
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
     * @var ArrayCollection $appRoles
     * @ORM\ManyToMany(targetEntity="AppRole")
     * @ORM\JoinTable(
     *     name="users_roles",
     *     joinColumns={@ORM\JoinColumn(name="userId", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="roleId", referencedColumnName="id")}
     * )
     */
    private $appRoles;

    /**
     * @param string $firstName
     * @param string $lastName
     * @throws \Exception
     */
    public function __construct(string $firstName, string $lastName)
    {
        $this->appRoles = new ArrayCollection();
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->id = Uuid::uuid4();
    }

    public function getId() : Uuid
    {
        return $this->id;
    }

    public function isNamed(string $fullNameToTest)
    {
        return $fullNameToTest === $this->firstName.' '.$this->lastName;
    }

    public function is(AppUser $registeredUser)
    {
        return $this->id->equals($registeredUser->id);
    }

    public function toDto() : UserDto
    {
        return new UserDto(
            $this->id->toString(),
            $this->firstName,
            $this->lastName,
            $this->appRoles->getValues()
        );
    }

    public function assignRole(AppRole $role)
    {
        $this->appRoles->add($role);
    }

    public function hasAppRole(AppRole $role)
    {
        $hasRoleCriteria =
            Criteria::create()->andWhere(
                Criteria::expr()->eq(
                    'id',
                    $role->getId()
                )
            );
        return $this->appRoles->matching($hasRoleCriteria)->count() > 0;
    }
}
