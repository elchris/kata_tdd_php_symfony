<?php

namespace AppBundle\Entity;

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
    private $appRoles;


    public function __construct(string $first, string $last)
    {
        $this->id = Uuid::uuid4();
        $this->first = $first;
        $this->last = $last;
        $this->appRoles = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function isNamed($nameToTest)
    {
        return $nameToTest === $this->first.' '.$this->last;
    }

    public function hasAppRole(AppRole $role)
    {
        $criteria = Criteria::create()->andWhere(
            Criteria::expr()->eq(
                'id',
                $role->getId()
            )
        );

        return $this
                ->appRoles
                ->matching($criteria)->count() > 0;
    }

    public function assignRole(AppRole $role)
    {
        $this->appRoles->add($role);
    }
}
