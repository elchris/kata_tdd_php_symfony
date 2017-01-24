<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="user_role")
 */
class UserRole
{

    /**
     * @var int $id
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var AppUser
     * @ORM\ManyToOne(targetEntity="AppUser", fetch="EAGER")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     *
     */
    private $user;

    /**
     * @var AppRole
     * @ORM\ManyToOne(targetEntity="AppRole", fetch="EAGER")
     * @ORM\JoinColumn(name="roleId", referencedColumnName="id")
     */
    private $role;

    /**
     * @param AppUser $savedUser
     * @param AppRole $role
     */
    public function __construct(AppUser $savedUser, AppRole $role)
    {
        $this->user = $savedUser;
        $this->role = $role;
    }
}
