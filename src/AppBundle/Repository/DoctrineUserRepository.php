<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Exception\MissingRoleException;
use AppBundle\Exception\UserWasNotFound;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Ramsey\Uuid\Uuid;

class DoctrineUserRepository extends AppRepository implements UserRepositoryInterface
{

    public function saveAndGet(AppUser $user) : AppUser
    {
        $this->save($user);
        return $user;
    }

    /**
     * @param Uuid $id
     * @return AppUser
     * @throws NonUniqueResultException
     * @throws UserWasNotFound
     */
    public function byId(Uuid $id) : AppUser
    {
        try {
            return $this->em->createQuery(
                'select u from E:AppUser u where u.id = :id'
            )
                ->setParameter('id', $id)
                ->getSingleResult();
        } catch (NoResultException $e) {
            throw new UserWasNotFound();
        }
    }

    /**
     * @param AppRole $roleToFind
     * @return AppRole
     * @throws MissingRoleException
     * @throws NonUniqueResultException
     */
    public function getRoleReference(AppRole $roleToFind) : AppRole
    {
        try {
            return $this->em->createQuery(
                'select r from E:AppRole r where r = :role'
            )
                ->setParameter('role', $roleToFind)
                ->getSingleResult();
        } catch (NoResultException $e) {
            throw new MissingRoleException('Role not found: '.$roleToFind->getName());
        }
    }
}
