<?php

namespace AppBundle\Entity;

use AppBundle\DTO\UserDto;
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
class AppUser
{
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
     * @var Uuid $id
     * @ORM\Id()
     * @ORM\Column(name="id", type="uuid", unique=true, nullable=false)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @var ArrayCollection $roles
     * @ORM\ManyToMany(targetEntity="AppRole")
     * @ORM\JoinTable(
     *     name="users_roles",
     *     joinColumns={@ORM\JoinColumn(name="userId", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="roleId", referencedColumnName="id")}
     * )
     */
    private $roles;

    /**
     * @var \DateTime $created
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */

    /**
     * @var \DateTime $created
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;

    /**
     * AppUser constructor.
     * @param string $firstName
     * @param string $lastName
     */
    public function __construct($firstName, $lastName)
    {
        $this->id = Uuid::uuid4();
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->roles = new ArrayCollection();
        $this->created = new \DateTime(null, new \DateTimeZone('UTC'));
    }

    /**
     * @return Uuid
     */
    public function getId() : Uuid
    {
        return $this->id;
    }

    public function assignRole(AppRole $role)
    {
        $this->roles->add($role);
    }

    public function hasRole(AppRole $role)
    {
        $hasRoleCriteria =
        Criteria::create()->andWhere(
            Criteria::expr()->eq(
                'id',
                $role->getId()
            )
        );
        return $this->roles->matching($hasRoleCriteria)->count() > 0;
    }

    public function isNamed(string $nameToCheck)
    {
        return $this->getFullName() === $nameToCheck;
    }

    public function is(AppUser $userToCompare)
    {
        return $this->getId()->equals($userToCompare->getId());
    }

    public function toDto()
    {
        return new UserDto(
            $this->id->toString(),
            $this->hasRole(AppRole::driver()),
            $this->hasRole(AppRole::passenger()),
            $this->getFullName()
        );
    }

    /**
     * @return string
     */
    private function getFullName(): string
    {
        return trim($this->firstName . ' ' . $this->lastName);
    }
}
