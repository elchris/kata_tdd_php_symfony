<?php


namespace AppBundle;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEvent;
use AppBundle\Entity\RideEventType;
use AppBundle\Entity\UserRole;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;

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
       $matchingRoleCount = $this->em->createQuery(
            'select count(distinct ur.id) from E:UserRole ur where ur.user = :user and ur.role = :role'
       )
       ->setParameter('user', $user)
       ->setParameter('role', $role)
       ->getSingleScalarResult();

       return ((int)$matchingRoleCount) === 1;
    }

    /**
     * @param float $lat
     * @param float $long
     * @return AppLocation
     */
    public function getOrCreateLocation($lat, $long)
    {
        try {
            return $this->getExistingLocation($lat, $long);
        } catch (NoResultException $e) {
            $this->save(new AppLocation($lat, $long));
            return $this->getExistingLocation($lat, $long);
        }
    }

    /**
     * @param $lat
     * @param $long
     * @return AppLocation
     */
    private function getExistingLocation($lat, $long)
    {
        $matchingLocation =
            $this->em->createQuery(
                'select l from E:AppLocation l where l.lat = :lat and l.long = :long'
            )
            ->setParameter('lat', $lat)
            ->setParameter('long', $long)
            ->getSingleResult();

        return $matchingLocation;
    }

    public function createRide(AppUser $passenger, AppLocation $departure)
    {
        $this->save(new Ride(
            $passenger,
            $departure
        ));
    }

    /**
     * @param AppUser $passenger
     * @return Ride[]
     */
    public function getRidesForPassenger(AppUser $passenger)
    {
        return $this->em->createQuery(
        'select r from E:Ride r where r.passenger = :passenger'
        )
        ->setParameter('passenger', $passenger)
        ->getResult();
    }

    /**
     * @param RideEventType $type
     * @return RideEventType
     */
    public function getEventType(RideEventType $type)
    {
        return $this->em->createQuery(
        'select t from E:RideEventType t where t = :type'
        )
        ->setParameter('type', $type)
        ->getSingleResult();
    }

    public function saveRideEvent(RideEvent $event)
    {
        $this->save($event);
    }

    /**
     * @param Ride $ride
     * @return RideEvent
     */
    public function getLastEventForRide(Ride $ride)
    {
        return $this->em->createQuery(
        'select e from E:RideEvent e where e.ride = :ride order by e.created desc, e.id desc'
        )
        ->setMaxResults(1)
        ->setParameter('ride', $ride)
        ->getSingleResult();
    }

    /**
     * @param Ride $ride
     * @param RideEventType $eventType
     * @return bool
     */
    public function isRideStatus(Ride $ride, RideEventType $eventType)
    {
        try {
            $lastEvent = $this->getLastEventForRide($ride);
            return $lastEvent->is($eventType);
        } catch (NoResultException $e) {
            return false;
        }
    }

    /**
     * @param Ride $ride
     * @param AppUser $driver
     */
    public function assignDriverToRide(Ride $ride, AppUser $driver)
    {
        $ride->assignDriver($driver);
        $this->save($ride);
    }
}
