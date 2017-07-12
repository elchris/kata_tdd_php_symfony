<?php


namespace Tests\AppBundle;

use AppBundle\AppService;
use AppBundle\Repository\LocationRepository;
use AppBundle\Repository\RideRepository;
use AppBundle\Repository\UserRepository;
use AppBundle\Service\LocationService;
use AppBundle\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AppTestCase extends WebTestCase
{

    /** @var  EntityManagerInterface */
    private $em;

    /** @var  AppService */
    protected $appService;

    /** @var UserService $userService */
    protected $userService;

    /** @var LocationService $locationService */
    protected $locationService;

    protected function setUp()
    {
        parent::setUp();
        self::bootKernel();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->setUpEntityManager();
        $this->appService = new AppService(
            new UserRepository($this->em()),
            new LocationRepository($this->em()),
            new RideRepository($this->em())
        );
        $this->userService = new UserService(
            new UserRepository($this->em())
        );
        $this->locationService = new LocationService(
            new LocationRepository($this->em())
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
        $tool->createSchema($classes);
    }
}
