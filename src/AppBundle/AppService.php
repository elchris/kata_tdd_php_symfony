<?php


namespace AppBundle;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use Doctrine\ORM\EntityManagerInterface;

class AppService
{
    /**
     * @var AppDao
     */
    private $dao;

    /**
     * @param AppDao $dao
     */
    public function __construct(AppDao $dao)
    {
        $this->dao = $dao;
    }

    public function newUser($firstName, $lastName)
    {
        $this->dao->newUser($firstName, $lastName);
    }

    /**
     * @param $userId
     * @return AppUser
     */
    public function getUserById($userId)
    {
        return $this->dao->getUserById($userId);
    }

    public function assignRoleToUser(AppUser $userOne, AppRole $role)
    {
        $this->dao->assignRoleToUser($userOne, $role);
    }

    /**
     * @param AppUser $user
     * @return bool
     */
    public function isUserDriver(AppUser $user)
    {
        return $this->dao->isUserInRole($user, AppRole::asPassenger());
    }
}
