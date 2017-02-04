<?php


namespace AppBundle;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEvent;
use AppBundle\Entity\RideEventType;

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

    public function newUser($firstName, $lastName)
    {
        $this->dao->newUser($firstName, $lastName);
    }

    /**
     * @param $userId
     * @return AppUser
     */
    public function getUserById($userId)
    {
        return $this->dao->getUserById($userId);
    }

    public function assignRoleToUser(AppUser $user, AppRole $role)
    {
        if (!$this->dao->isUserInRole($user, $role)) {
            $this->dao->assignRoleToUser($user, $role);
        } else {
            throw new RoleLifeCycleException(
                'User: '
                .$user->getFullName()
                .' is already of Role: '
                .$role->getName()
            );
        }
    }

    /**
     * @param AppUser $user
     * @return bool
     */
    public function isUserPassenger(AppUser $user)
    {
        return $this->dao->isUserInRole($user, AppRole::asPassenger());
    }

    public function isUserDriver(AppUser $user)
    {
        return $this->dao->isUserInRole($user, AppRole::asDriver());
    }

    /**
     * @param float $lat
     * @param float $long
     * @return AppLocation
     */
    public function getLocation($lat, $long)
    {
        return $this->dao->getOrCreateLocation(
            $lat,
            $long
        );
    }

    public function createRide(AppUser $passenger, AppLocation $departure)
    {
        $this->dao->createRide($passenger, $departure);
    }

    /**
     * @param AppUser $passenger
     * @return Ride[]
     */
    public function getRidesForPassenger(AppUser $passenger)
    {
        return $this->dao->getRidesForPassenger($passenger);
    }

    public function markRideAsRequested(Ride $ride)
    {
        $event = new RideEvent(
            $this->dao->getEventType(RideEventType::asRequested()),
            $ride,
            $ride->getPassenger()
        );
        $this->dao->saveRideEvent($event);
    }

    /**
     * @param Ride $ride
     * @return RideEvent
     */
    public function getRideStatus(Ride $ride)
    {
        return $this->dao->getLastEventForRide($ride);
    }
}
