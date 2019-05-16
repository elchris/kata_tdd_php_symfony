<?php

namespace AppBundle\Entity;

use AppBundle\DTO\UserDto;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * Class AppUser
 * @package AppBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="users")
 */
class AppUser extends BaseUser
{
    /**
     * @var string $firstName
     * @ORM\Column(name="first_name", type="string", nullable=true)
     */
    private $firstName;

    /**
     * @var string $lastName
     * @ORM\Column(name="last_name", type="string", nullable=true)
     */
    private $lastName;

    /**
     * @var Uuid $id
     * @ORM\Id()
     * @ORM\Column(name="id", type="uuid", unique=true, nullable=false)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    protected $id;

    /**
     * @var ArrayCollection $roles
     * @ORM\ManyToMany(targetEntity="AppRole")
     * @ORM\JoinTable(
     *     name="users_roles",
     *     joinColumns={@ORM\JoinColumn(name="userId", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="roleId", referencedColumnName="id")}
     * )
     */
    private $appRoles;

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
     * @param string $email
     * @param $username
     * @param $password
     * @throws \Exception
     */
    public function __construct(
        $firstName = null,
        $lastName = null,
        $email = null,
        $username = null,
        $password = null
    ) {
        parent::__construct();
        $this->id = Uuid::uuid4();
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->username = $username;
        $this->setPlainPassword($password);
        $this->appRoles = new ArrayCollection();
        $this->created = new \DateTime(null, new \DateTimeZone('UTC'));
    }

    public function assignRole(AppRole $role): void
    {
        $this->appRoles->add($role);
    }

    public function userHasRole(AppRole $role): bool
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

    public function isNamed(string $nameToCheck): bool
    {
        return $this->getFullName() === $nameToCheck;
    }

    public function is(AppUser $userToCompare): bool
    {
        return $this->getId()->equals($userToCompare->getId());
    }

    public function toDto(): UserDto
    {
        return new UserDto(
            $this->id->toString(),
            $this->userHasRole(AppRole::driver()),
            $this->userHasRole(AppRole::passenger()),
            $this->getFullName(),
            $this->getUsername(),
            $this->getEmail()
        );
    }

    /**
     * @return string
     */
    private function getFullName(): string
    {
        return trim($this->firstName . ' ' . $this->lastName);
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }
}
