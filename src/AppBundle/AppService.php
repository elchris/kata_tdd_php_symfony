<?php


namespace AppBundle;

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
}
