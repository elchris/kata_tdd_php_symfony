<?php


namespace AppBundle;

use AppBundle\Entity\AppUser;
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

    public function newUser($firstName, $lastName)
    {
        $user = new AppUser($firstName, $lastName);
        $this->save($user);
    }

    protected function save($user)
    {
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @param $userId
     * @return AppUser
     */
    public function getUserById($userId)
    {
        return $this->em->createQuery(
            'select u from E:AppUser u where u.id = :userId'
        )
        ->setParameter('userId', $userId)
        ->getSingleResult();
    }
}
