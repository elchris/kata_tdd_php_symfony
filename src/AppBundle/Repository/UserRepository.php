<?php


namespace AppBundle\Repository;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Entity\UserRole;
use AppBundle\Exception\RoleLifeCycleException;

class UserRepository extends AppRepository
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
        $this->verifyPassengerRole($user, $passengerRole);
        $userPassengerRole = new UserRole($user, $passengerRole);
        $this->save($userPassengerRole);
    }

    public function makeUserDriver(AppUser $user)
    {
        $driverRole = $this->getDriverRole();
        $this->verifyDriverRole($user, $driverRole);
        $userDriverRole = new UserRole($user, $driverRole);
        $this->save($userDriverRole);
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
        return
            $this->em
                ->createQueryBuilder()
                ->select(['ur'])
                ->from(UserRole::class, 'ur')
                ->where('ur.user = :user')
                ->andWhere('ur.role = :role')
                ->setParameter('user', $user)
                ->setParameter('role', $role)
                ->getQuery()
                ->getResult()
        ;
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

    /**
     * @param AppUser $user
     * @param AppRole $passengerRole
     */
    private function verifyPassengerRole(AppUser $user, AppRole $passengerRole)
    {
        if ($this->isUserPassenger($user)) {
            $this->throwRoleLifeCycleException($user, $passengerRole);
        }
    }

    /**
     * @param AppUser $user
     * @param AppRole $driverRole
     */
    private function verifyDriverRole(AppUser $user, AppRole $driverRole)
    {
        if ($this->isUserDriver($user)) {
            $this->throwRoleLifeCycleException($user, $driverRole);
        }
    }
}
