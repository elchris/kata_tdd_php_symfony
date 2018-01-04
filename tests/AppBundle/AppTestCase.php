<?php


namespace Tests\AppBundle;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEventType;
use AppBundle\Repository\LocationRepository;
use AppBundle\Repository\RideEventRepository;
use AppBundle\Repository\RideRepository;
use AppBundle\Repository\UserRepository;
use AppBundle\Service\LocationService;
use AppBundle\Service\RideService;
use AppBundle\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AppTestCase extends WebTestCase
{
    const HOME_LOCATION_LAT = 37.773160;
    const HOME_LOCATION_LONG = -122.432444;
    const WORK_LOCATION_LAT = 37.7721718;
    const WORK_LOCATION_LONG = -122.4310872;

    /** @var  EntityManagerInterface */
    private $em;

    protected function setUp()
    {
        parent::setUp();
        self::bootKernel();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->setUpEntityManager();

        $this->userRepository = new UserRepository($this->em());
        $this->userService = new UserService(new UserRepository($this->em()));
        $this->locationService = new LocationService(new LocationRepository($this->em()));
        $this->rideRepository = new RideRepository($this->em());
        $this->rideEventRepository = new RideEventRepository($this->em());
        $this->rideService = new RideService(
            $this->rideRepository,
            $this->rideEventRepository
        );
    }

    protected function em()
    {
        return $this->em;
    }

    protected function save($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    private function setUpEntityManager()
    {
        $classes = $this->em()->getMetadataFactory()->getAllMetadata();
        $tool = new SchemaTool($this->em);
        $tool->dropSchema($classes);
        try {
            $tool->createSchema($classes);
        } catch (ToolsException $e) {
        }
    }

    /** @var  UserRepository */
    protected $userRepository;

    /** @var  UserService */
    protected $userService;

    /** @var  LocationService */
    protected $locationService;

    /** @var  RideRepository */
    protected $rideRepository;

    /** @var  RideEventRepository */
    protected $rideEventRepository;

    /** @var  RideService */
    protected $rideService;

    /** @var AppUser $savedPassenger */
    protected $savedPassenger;

    /**
     * @return AppUser
     */
    protected function getSavedUser()
    {
        return $this->getSavedUserWithName('chris', 'holland');
    }

    protected function getSavedUserWithName($first, $last)
    {
        return $this->userService->newUser($first, $last);
    }

    /**
     * @return AppUser
     */
    protected function getNewPassenger()
    {
        $passenger = $this->getSavedUser();
        $this->makeUserPassenger($this->getSavedUser());
        return $passenger;
    }

    /**
     * @param $user
     */
    protected function makeUserPassenger(AppUser $user)
    {
        $this->userService->makeUserPassenger($user);
    }

    /**
     * @param $driver
     */
    protected function makeUserDriver(AppUser $driver)
    {
        $this->userService->makeUserDriver($driver);
    }

    /**
     * @param $user
     * @return bool
     */
    protected function isPassenger(AppUser $user)
    {
        return $this->userService->isPassenger($user);
    }

    /**
     * @return AppUser
     */
    protected function getNewDriver()
    {
        return $this->getNewDriverWithName('new', 'driver');
    }

    protected function getNewDriverWithName($first, $last)
    {
        $driver = $this->getSavedUserWithName($first, $last);
        $this->makeUserDriver($driver);
        return $driver;
    }

    /**
     * @return Ride
     */
    protected function getSavedRide()
    {
        $ridePassenger = $this->getNewPassenger();
        $this->savedPassenger = $this->getServiceUserById($ridePassenger->getId());
        $departure = $this->getSavedHomeLocation();
        $ride = new Ride($this->savedPassenger, $departure);
        $this->rideRepository->save($ride);
        return $ride;
    }

    /**
     * @return AppLocation
     */
    protected function getSavedHomeLocation()
    {
        return $this->locationService->getLocation(
            self::HOME_LOCATION_LAT,
            self::HOME_LOCATION_LONG
        );
    }

    /**
     * @param $userId
     * @return AppUser
     */
    protected function getServiceUserById($userId)
    {
        return $this->userService->getUserById($userId);
    }

    /**
     * @param $passenger
     * @param $departure
     * @return Ride
     * @throws \AppBundle\Exception\UserNotPassengerException
     */
    protected function getNewRide(AppUser $passenger, AppLocation $departure)
    {
        return $this->rideService->newRide($passenger, $departure);
    }

    /**
     * @param $rideInProgress
     * @param $driver
     * @return Ride
     */
    protected function markRideCompleted(Ride $rideInProgress, AppUser $driver)
    {
        return $this->rideService->markRideCompleted($rideInProgress, $driver);
    }

    /**
     * @param $ride
     * @return RideEventType
     */
    protected function getRideStatus(Ride $ride)
    {
        return $this->rideService->getRideStatus($ride);
    }

    /**
     * @param $acceptedRide
     * @param $driver
     * @return Ride
     */
    protected function markRideInProgress(Ride $acceptedRide, AppUser $driver)
    {
        return $this->rideService->markRideInProgress($acceptedRide, $driver);
    }

    /**
     * @param $newRide
     * @param $driver
     * @return Ride
     */
    protected function acceptRide(Ride $newRide, AppUser $driver)
    {
        return $this->rideService->acceptRide($newRide, $driver);
    }

    /**
     * @return Ride
     * @throws \AppBundle\Exception\UserNotPassengerException
     */
    protected function getSavedNewRideWithPassengerAndDestination()
    {
        $passenger = $this->getSavedUser();
        $this->makeUserPassenger($passenger);

        $departure = $this->getSavedHomeLocation();

        /** @var Ride $newRide */
        $newRide = $this->getNewRide(
            $passenger,
            $departure
        );

        return $newRide;
    }

    /**
     * @param AppUser $driver
     * @return Ride
     * @throws \AppBundle\Exception\UserNotPassengerException
     */
    protected function getRideInProgress(AppUser $driver)
    {
        $newRide = $this->getSavedNewRideWithPassengerAndDestination();
        $acceptedRide = $this->acceptRide($newRide, $driver);
        return $this->markRideInProgress($acceptedRide, $driver);
    }

    /**
     * @return Ride
     * @throws \AppBundle\Exception\UserNotPassengerException
     */
    protected function getAcceptedRide()
    {
        $newDriver = $this->getNewDriver();
        return $this->getAcceptedRideWithDriver($newDriver);
    }

    /**
     * @param AppUser $driver
     * @return Ride
     * @throws \AppBundle\Exception\UserNotPassengerException
     */
    protected function getAcceptedRideWithDriver(AppUser $driver)
    {
        $newRide = $this->getSavedNewRideWithPassengerAndDestination();
        return $this->acceptRide($newRide, $driver);
    }
}
