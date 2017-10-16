<?php


namespace Tests\AppBundle;

use AppBundle\Entity\AppUser;
use AppBundle\Repository\LocationRepository;
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
}
