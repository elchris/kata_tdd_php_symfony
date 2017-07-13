<?php


namespace Tests\AppBundle;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Entity\RideEventType;
use AppBundle\Repository\LocationRepository;
use AppBundle\Repository\RideRepository;
use AppBundle\Repository\UserRepository;
use AppBundle\Service\LocationService;
use AppBundle\Service\RideService;
use AppBundle\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AppTestCase extends WebTestCase
{

    /** @var  EntityManagerInterface */
    private $em;

    /** @var UserService $userService */
    protected $userService;

    /** @var LocationService $locationService */
    protected $locationService;

    /** @var RideService $rideService */
    protected $rideService;

    /** @var  AppLocation */
    protected $home;
    /** @var  AppUser */
    protected $userOne;
    /** @var  AppUser */
    protected $userTwo;

    /** @var  AppUser */
    protected $prospectiveDriver;
    /** @var AppLocation */
    protected $work;

    protected function setUp()
    {
        parent::setUp();
        self::bootKernel();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->setUpEntityManager();
        $this->bootStrapServices();
        $this->hydrateTestData();
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

    protected function hydrateTestData()
    {
        $this->userService->newUser('Chris', 'Holland');
        $this->userService->newUser('Scott', 'Sims');
        $this->userService->newUser('Prospective', 'Driver');
        /** @var AppUser $user */
        $this->userTwo = $this->userService->getUserById(2);
        $this->userOne = $this->userService->getUserById(1);
        $this->prospectiveDriver = $this->userService->getUserById(3);

        $this->home = $this->locationService->getLocation(
            37.773160,
            -122.432444
        );

        $this->work = $this->locationService->getLocation(
            37.7721718,
            -122.4310872
        );

        $this->save(AppRole::asPassenger());
        $this->save(AppRole::asDriver());

        $this->userService->assignRoleToUser($this->prospectiveDriver, AppRole::asDriver());

        $this->save(RideEventType::asRequested());
        $this->save(RideEventType::asAccepted());
        $this->save(RideEventType::inProgress());
        $this->save(RideEventType::asCancelled());
        $this->save(RideEventType::asCompleted());
        $this->save(RideEventType::asRejected());
        $this->save(RideEventType::asDestination());
    }

    protected function bootStrapServices()
    {
        $this->userService = new UserService(
            new UserRepository($this->em())
        );
        $this->locationService = new LocationService(
            new LocationRepository($this->em())
        );
        $this->rideService = new RideService(
            new RideRepository($this->em())
        );
    }
}
