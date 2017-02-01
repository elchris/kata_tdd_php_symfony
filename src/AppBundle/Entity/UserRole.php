<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class UserRole
 * @package AppBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="users_roles")
 */
class UserRole
{
    /**
     * @var integer $id
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id;

    /**
     * @var AppUser
     * @ORM\ManyToOne(targetEntity="AppUser", fetch="EAGER")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    private $user;
    /**
     * @var AppRole
     * @ORM\ManyToOne(targetEntity="AppRole", fetch="EAGER")
     * @ORM\JoinColumn(name="roleId", referencedColumnName="id")
     */
    private $role;

    /**
     * @param AppUser $user
     * @param AppRole $role
     */
    public function __construct(AppUser $user, AppRole $role)
    {
        $this->user = $user;
        $this->role = $role;
    }
}
