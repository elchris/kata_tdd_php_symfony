<?php

namespace AppBundle\Entity;

use AppBundle\Dto\UserDto;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Ramsey\Uuid\Uuid;
use FOS\UserBundle\Model\User as BaseUser;

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
    protected $id;
    /**
     * @var string
     * @ORM\Column(name="first", type="string", nullable=false)
     */
    private $first;
    /**
     * @var string
     * @ORM\Column(name="last", type="string", nullable=false)
     */
    private $last;
    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppRole")
     * @ORM\JoinTable(
     *     name="users_roles",
     *     joinColumns={@ORM\JoinColumn(name="userId", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="roleId", referencedColumnName="id")}
     * )
     */
    private $userRoles;

    /**
     * AppUser constructor.
     * @param string $first
     * @param string $last
     * @throws Exception
     */
    public function __construct(string $first, string $last)
    {
        $this->id = Uuid::uuid4();
        $this->first = $first;
        $this->last = $last;
        $this->userRoles = new ArrayCollection();
    }

    public function getId() : Uuid
    {
        return $this->id;
    }

    public function isNamed(string $nameToCheck)
    {
        return $nameToCheck === $this->first.' '.$this->last;
    }

    public function is(AppUser $registeredUser) : bool
    {
        return $registeredUser->id->equals($this->id);
    }

    public function toDto() : UserDto
    {
        return new UserDto(
            $this->id->toString(),
            $this->first,
            $this->last,
            $this->userRoles->toArray()
        );
    }

    public function assignRole(AppRole $roleToAssign)
    {
        $this->userRoles->add($roleToAssign);
    }

    public function hasRole(AppRole $roleToVerify)
    {
        $hasRoleCriteria =
            Criteria::create()->andWhere(
                Criteria::expr()->eq(
                    'id',
                    $roleToVerify->getId()
                )
            );
        return $this->userRoles->matching($hasRoleCriteria)->count() > 0;
    }
}
