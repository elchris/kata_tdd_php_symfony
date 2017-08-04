<?php


namespace AppBundle\Repository;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;

class UserRepository extends AppRepository implements UserRepositoryInterface
{
    public function newUser($firstName, $lastName)
    {
        $user = new AppUser($firstName, $lastName);
        $this->save($user);
    }

    /**
     * @param integer $userId
     * @return AppUser
     */
    public function getUserById($userId)
    {
        return $this->query(
            'select u from E:AppUser u where u.id = :userId'
        )
            ->setParameter('userId', $userId)
            ->getSingleResult();
    }

    public function assignRoleToUser(AppUser $user, AppRole $role)
    {
        $user->addRole($this->getStoredRole($role));
        $this->save($user);
    }

    private function getStoredRole(AppRole $role)
    {
        return $this->query(
            'select r from E:AppRole r where r = :role'
        )
            ->setParameter('role', $role)
            ->getSingleResult();
    }

    public function isUserInRole(AppUser $user, AppRole $role)
    {
        return $user->hasRole($this->getStoredRole($role));
    }
}
