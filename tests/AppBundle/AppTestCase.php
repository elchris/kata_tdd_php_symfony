<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Repository\LocationRepository;
use AppBundle\Repository\RideRepository;
use AppBundle\Repository\UserRepository;
use AppBundle\Service\LocationService;
use AppBundle\Service\RideService;
use AppBundle\Service\UserSvc;
use Exception;
use FOS\UserBundle\Model\UserManagerInterface;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\AppBundle\User\FakeUserManager;

abstract class AppTestCase extends WebTestCase
{
    /** @var  EntityManagerInterface */
    private $em;

    /** @var UserManagerInterface */
    private $userManager;
    /**
     * @var UserRepository
     */
    protected $userRepository;
    /**
     * @var UserSvc
     */
    protected $userService;
    /**
     * @var LocationRepository
     */
    protected $locationRepository;

    const HOME_LOCATION_LAT = 37.773160;
    const HOME_LOCATION_LONG = -122.432444;

    const WORK_LOCATION_LAT = 37.7721718;
    const WORK_LOCATION_LONG = -122.4310872;
    /**
     * @var RideRepository
     */
    protected $rideRepository;
    /**
     * @var LocationService
     */
    protected $locationService;
    /**
     * @var RideService
     */
    protected $rideService;

    protected function setUp()
    {
        parent::setUp();
        self::bootKernel();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->userManager = new FakeUserManager($this->em());
        $this->setUpEntityManager();

        $this->userRepository = new UserRepository($this->em());
        $this->userService = new UserSvc($this->userRepository);
        $this->locationRepository = new LocationRepository(
            $this->em()
        );
        $this->rideRepository = new RideRepository($this->em());
        $this->locationService = new LocationService(
            $this->locationRepository
        );
        $this->rideService = new RideService(
            $this->rideRepository
        );

        //TODO: add roles to migration, or manually to DB table
        $this->save(AppRole::passenger());
        $this->save(AppRole::driver());
    }

    protected function em()
    {
        return $this->em;
    }

    protected function save($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
        return $entity;
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

    /**
     * @param string $class
     * @param string $message
     */
    protected function verifyExceptionWithMessage(string $class, string $message): void
    {
        $this->expectException($class);
        $this->expectExceptionMessage($message);
    }

    /**
     * @return AppUser
     * @throws Exception
     */
    protected function getRepoSavedUser(): AppUser
    {
        $newUser = new AppUser('chris', 'holland');
        self::assertNotNull($newUser->getId());

        $savedUser = $this->userRepository->saveUser($newUser);

        return $newUser;
    }


    /**
     * @return AppUser
     * @throws Exception
     */
    protected function getRepoPassenger(): AppUser
    {
        $roleToAssign = AppRole::passenger();
        return $this->getRepoUserWithRole($roleToAssign);
    }

    /**
     * @return AppUser
     * @throws Exception
     */
    protected function getRepoDriver(): AppUser
    {
        $roleToAssign = AppRole::driver();
        return $this->getRepoUserWithRole($roleToAssign);
    }

    /**
     * @return AppLocation
     * @throws Exception
     */
    protected function getRepoHomeLocation(): AppLocation
    {
        $homeLocation = new AppLocation(
            self::HOME_LOCATION_LAT,
            self::HOME_LOCATION_LONG
        );

        $this->locationRepository->getOrCreateLocation($homeLocation);

        return $homeLocation;
    }

    /**
     * @param AppRole $roleToAssign
     * @return AppUser
     * @throws Exception
     */
    protected function getRepoUserWithRole(AppRole $roleToAssign): AppUser
    {
        $user = $this->getRepoSavedUser();
        $user->assignRole(
            $this->userRepository->getRoleReference(
                $roleToAssign
            )
        );

        return $this->userRepository->saveUser($user);
    }
}
