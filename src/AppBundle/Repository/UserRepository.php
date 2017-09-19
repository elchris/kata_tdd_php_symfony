<?php


namespace AppBundle\Repository;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use Doctrine\ORM\EntityManagerInterface;

class UserRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(AppUser $newUser)
    {
        $this->em->persist($newUser);
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

    public function assignRoleToUser(AppUser $user, AppRole $role)
    {
        $user->assignRole($role);
        $this->save($user);
    }
}
