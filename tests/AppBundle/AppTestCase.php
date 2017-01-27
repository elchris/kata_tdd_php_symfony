<?php


namespace Tests\AppBundle;

use AppBundle\Repository\UserRepository;
use AppBundle\Service\AppUserService;
use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEventType;
use AppBundle\Repository\RideRepository;
use AppBundle\Service\RideService;
use AppBundle\Exception\RoleLifeCycleException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AppTestCase extends WebTestCase
{
    const USER_ONE_FIRST_NAME = 'Chris';
    const USER_ONE_LAST_NAME = 'Holland';
    const USER_TWO_FIRST_NAME = 'Scott';
    const USER_TWO_LAST_NAME = 'Sims';
    /** @var  EntityManagerInterface */
    private $em;

    /** @var AppUserService */
    private $appUserService;

    /** @var  RideService */
    private $rideService;

    /** @var AppUser */
    protected $savedUserOne;
    /** @var AppUser */
    protected $savedUserTwo;

    /** @var  AppLocation */
    protected $home;
    /** @var AppLocation */
    protected $work;

    /** @var AppLocation */
    protected $dupeHome;
    /** @var AppLocation */
    protected $dupeWork;

    protected function setUp()
    {
        parent::setUp();
        self::bootKernel();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->appUserService = new AppUserService(new UserRepository($this->em()));
        $this->rideService = new RideService(new RideRepository($this->em()));
        $this->setUpEntityManager();

        $this->setTestLocations();
        $this->setTestUsers();

        $this->setEditorialRoles();
        $this->setEditorialRideEventTypes();
    }

    protected function em()
    {
        return $this->em;
    }

    /**
     * @return AppUserService
     */
    protected function user()
    {
        return $this->appUserService;
    }

    /**
     * @return RideService
     */
    protected function ride()
    {
        return $this->rideService;
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


    protected function makeDriver(AppUser $user)
    {
        $this->user()->makeUserDriver($user);
    }

    protected function makePassenger(AppUser $user)
    {
        $this->user()->makeUserPassenger($user);
    }

    /**
     * @param $roleName
     */
    protected function setUpDuplicateRoleThrowsException($roleName)
    {
        $this->makePassenger($this->savedUserOne);
        $this->makeDriver($this->savedUserOne);

        self::expectException(RoleLifeCycleException::class);
        self::expectExceptionMessage(
            'User: '
            . $this->savedUserOne->getFirstName()
            . ' '
            . $this->savedUserOne->getLastName()
            . ' '
            . 'is Already a ' . $roleName
        );
    }

    /**
     * @param AppUser $user
     * @return bool
     */
    protected function isPassenger(AppUser $user)
    {
        return $this->user()->isUserPassenger($user);
    }

    protected function isDriver(AppUser $user)
    {
        return $this->user()->isUserDriver($user);
    }

    /**
     * @return Ride
     */
    protected function getUserRide()
    {
        $this->ride()->createRideForUser(
            $this->savedUserOne,
            $this->home,
            $this->work
        );
        /** @var Ride[] $rides */
        $rides = $this->user()->getRidesForUser($this->savedUserOne);
        self::assertCount(1, $rides);
        $ride = $rides[0];

        return $ride;
    }

    /**
     * @return Ride
     */
    protected function makeRideForPassengerAndDriver()
    {
        $this->makePassenger($this->savedUserOne);
        $this->makeDriver($this->savedUserTwo);

        $ride = $this->getUserRide();
        $this->ride()->markRideRequested(
            $ride,
            $this->savedUserOne
        );
        $this->ride()->markRideAsAcceptedByDriver(
            $ride,
            $this->savedUserTwo
        );

        return $ride;
    }

    protected function setTestLocations()
    {
        $this->home = new AppLocation(30.3366446, -97.7375456);
        $this->work = new AppLocation(30.4302982, -97.7471131);

        $this->dupeHome = new AppLocation(30.3366446, -97.7375456);
        $this->dupeWork = new AppLocation(30.4302982, -97.7471131);
    }

    protected function setTestUsers()
    {
        $userOne = new AppUser(self::USER_ONE_FIRST_NAME, self::USER_ONE_LAST_NAME);
        $this->user()->saveUser($userOne);

        $userTwo = new AppUser(self::USER_TWO_FIRST_NAME, self::USER_TWO_LAST_NAME);
        $this->user()->saveUser($userTwo);

        /** @var AppUser $savedUser */
        $this->savedUserOne = $this->user()->getUserById(1);
        $this->savedUserTwo = $this->user()->getUserById(2);
    }

    protected function setEditorialRoles()
    {
        $passengerRole = AppRole::asPassenger();
        $driverRole = AppRole::asDriver();
        $this->save($passengerRole);
        $this->save($driverRole);
    }

    protected function setEditorialRideEventTypes()
    {
        $requested = RideEventType::asRequested();
        $acceptedByDriver = RideEventType::asAcceptedByDriver();
        $inProgress = RideEventType::asInProgress();
        $cancelled = RideEventType::asCancelled();
        $completed = RideEventType::asCompleted();
        $deniedByDriver = RideEventType::asRejectedByDriver();
        $requestTimedOut = RideEventType::asRequestTimedOut();

        $this->save($requested);
        $this->save($acceptedByDriver);
        $this->save($inProgress);
        $this->save($cancelled);
        $this->save($completed);
        $this->save($deniedByDriver);
        $this->save($requestTimedOut);
    }
}
