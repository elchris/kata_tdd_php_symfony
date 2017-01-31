<?php


namespace AppBundle;

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
}
