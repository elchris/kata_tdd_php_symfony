<?php


namespace AppBundle;

use Doctrine\ORM\EntityManagerInterface;

class AppDao
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }
}
