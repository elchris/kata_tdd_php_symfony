<?php


namespace AppBundle\Repository;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Entity\UserRole;
use AppBundle\Exception\RoleLifeCycleException;

class AppUserDao extends AppDao
{
    /**
     * @param AppRole $role
     * @@return AppRole
     */
    private function getRole(AppRole $role)
    {
        return $this->em->createQuery(
            'select role from E:AppRole role where role = :role'
        )
        ->setParameter('role', $role)
        ->getSingleResult();
    }

    /**
     * @return AppRole
     */
    private function getPassengerRole()
    {
        return $this->getRole(AppRole::asPassenger());
    }

    public function getDriverRole()
    {
        return $this->getRole(AppRole::asDriver());
    }

    public function makeUserPassenger(AppUser $user)
    {
        $passengerRole = $this->getPassengerRole();
        if ($this->isUserPassenger($user)) {
            $this->throwRoleLifeCycleException($user, $passengerRole);
        } else {
            $userPassengerRole = new UserRole($user, $passengerRole);
            $this->save($userPassengerRole);
        }
    }

    public function makeUserDriver(AppUser $user)
    {
        $driverRole = $this->getDriverRole();
        if ($this->isUserDriver($user)) {
            $this->throwRoleLifeCycleException($user, $driverRole);
        } else {
            $userDriverRole = new UserRole($user, $driverRole);
            $this->save($userDriverRole);
        }
    }

    public function saveUser(AppUser $user)
    {
        $this->save($user);
    }

    /**
     * @param int $userId
     * @return AppUser
     */
    public function getUserById($userId)
    {
        return $this->em->createQuery(
            'select u from E:AppUser u where u.id = :id'
        )
        ->setParameter('id', $userId)
        ->getSingleResult()
        ;
    }

    public function isUserPassenger(AppUser $user)
    {
        return sizeof($this->getRolesForUser(
            $user,
            $this->getPassengerRole()
        )) === 1;
    }

    public function isUserDriver(AppUser $user)
    {
        return sizeof($this->getRolesForUser(
                $user,
                $this->getDriverRole()
            )) === 1;
    }

    /**
     * @param AppUser $user
     * @param AppRole $role
     * @return UserRole[]
     */
    protected function getRolesForUser(AppUser $user, AppRole $role)
    {
        $userRoles = $this->em->createQuery(
            'select ur
                from E:UserRole ur
                where ur.user = :user
                and ur.role = :role
            '
        )
            ->setParameter('user', $user)
            ->setParameter('role', $role)
            ->getResult();
        return $userRoles;
    }

    /**
     * @param AppUser $savedUser
     * @param AppRole $role
     * @throws RoleLifeCycleException
     */
    protected function throwRoleLifeCycleException(AppUser $savedUser, AppRole $role)
    {
        throw new RoleLifeCycleException(
            'User: '
            . $savedUser->getFirstName()
            . ' '
            . $savedUser->getLastName()
            . ' is Already a '
            . $role->getName()
        );
    }

    /**
     * @param AppUser $user
     * @return Ride[]
     */
    public function getRidesForUser(Appuser $user)
    {
        return $this->em->createQuery(
            '
                select r
                    from E:Ride r
                    where r.passenger = :user                    
            '
        )
        ->setParameter('user', $user)
        ->getResult()
        ;
    }

    /**
     * @return AppLocation[]
     */
    public function getAllLocations()
    {
        return $this->em->createQuery(
            'select l from E:AppLocation l'
        )
        ->getResult();
    }
}
