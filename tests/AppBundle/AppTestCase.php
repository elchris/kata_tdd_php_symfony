<?php


namespace Tests\AppBundle;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Repository\LocationRepository;
use AppBundle\Repository\RideEventRepository;
use AppBundle\Repository\RideRepository;
use AppBundle\Repository\UserRepository;
use AppBundle\Service\LocationService;
use AppBundle\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
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
        $tool->createSchema($classes);
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

    /**
     * @return AppUser
     */
    protected function getSavedUser()
    {
        return $this->getSavedUserWithName('chris', 'holland');
    }

    protected function getSavedUserWithName($first, $last)
    {
        $user = new AppUser($first, $last);

        $this->userRepository->save($user);

        return $user;
    }

    /** @var AppUser $savedPassenger */
    protected $savedPassenger;

    /**
     * @return Ride
     */
    protected function getSavedRide()
    {
        $user = $this->getSavedUser();
        $this->userService->makeUserPassenger($user);
        $this->savedPassenger = $this->userService->getUserById($user->getId());
        $departure = $this->getSavedHomeLocation();
        $ride = new Ride($this->savedPassenger, $departure);
        $this->rideRepository->save($ride);
        return $ride;
    }

    /**
     * @return \AppBundle\Entity\AppLocation
     */
    protected function getSavedHomeLocation()
    {
        $departure = $this->locationService->getLocation(
            self::HOME_LOCATION_LAT,
            self::HOME_LOCATION_LONG
        );

        return $departure;
    }
}
