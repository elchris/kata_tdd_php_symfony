<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Exception\DuplicateRoleAssignmentException;
use AppBundle\Exception\UserNotFoundException;
use Ramsey\Uuid\Uuid;

class UserRepository extends AppRepository
{
    /**
     * @param Uuid $userId
     * @return AppUser
     * @throws UserNotFoundException
     */
    public function getUserById(Uuid $userId)
    {
        try {
            return $this->em->createQuery(
                'select u from E:AppUser u where u.id = :userId'
            )
                ->setParameter('userId', $userId)
                ->getSingleResult();
        } catch (\Exception $e) {
            throw new UserNotFoundException();
        }
    }

    /**
     * @param AppUser $user
     * @param AppRole $role
     * @throws DuplicateRoleAssignmentException
     */
    public function assignRoleToUser(AppUser $user, AppRole $role)
    {
        if ($user->hasRole($role)) {
            throw new DuplicateRoleAssignmentException();
        }
        $role = $this->getRoleReference($role);
        $user->assignRole($role);
        $this->save($user);
    }


    /**
     * @param AppRole $role
     * @return null | AppRole
     */
    private function getRoleReference(AppRole $role)
    {
        /** @var AppRole $role */
        $role = $this->em->getRepository(AppRole::class)->findOneBy(
            [
                'id' => $role->getId()
            ]
        );
        return $role;
    }
}
