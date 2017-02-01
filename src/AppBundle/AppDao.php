<?php


namespace AppBundle;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\UserRole;
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

    public function assignRoleToUser(AppUser $user, AppRole $role)
    {
        $storedRole = $this->getStoredRole($role);
        $this->save(new UserRole($user, $storedRole));
    }

    private function getStoredRole(AppRole $role)
    {
        return $this->em->createQuery(
            'select r from E:AppRole r where r = :role'
        )
        ->setParameter('role', $role)
        ->getSingleResult();
    }

    public function isUserInRole(AppUser $user, AppRole $role)
    {
       $matchingRoles = $this->em->createQuery(
            'select ur from E:UserRole ur where ur.user = :user and ur.role = :role'
       )
       ->setParameter('user', $user)
       ->setParameter('role', $this->getStoredRole($role))
       ->getResult();

       return sizeof($matchingRoles) === 1;
    }
}
