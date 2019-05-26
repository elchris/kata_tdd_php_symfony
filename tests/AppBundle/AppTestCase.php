<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppLocation;
use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Repository\LocationRepository;
use AppBundle\Repository\RideRepository;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use FOS\UserBundle\Model\UserManagerInterface;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\AppBundle\User\FakeUserManager;

abstract class AppTestCase extends WebTestCase
{
    const HOME_LOCATION_LAT = 37.773160;
    const HOME_LOCATION_LONG = -122.432444;

    const WORK_LOCATION_LAT = 37.7721718;
    const WORK_LOCATION_LONG = -122.4310872;

    /** @var  EntityManagerInterface */
    private $em;

    /** @var UserManagerInterface */
    private $userManager;

    /**
     * @var UserRepository
     */
    protected $userRepo;
    /**
     * @var LocationRepository
     */
    protected $locationRepo;
    /**
     * @var RideRepository
     */
    protected $rideRepository;

    protected function setUp()
    {
        parent::setUp();
        self::bootKernel();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->userManager = new FakeUserManager($this->em());
        $this->setUpEntityManager();

        $this->save(AppRole::passenger());
        $this->save(AppRole::driver());

        $this->userRepo = new UserRepository($this->em());
        $this->locationRepo = new LocationRepository($this->em());
        $this->rideRepository = new RideRepository($this->em());
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
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    protected function getRepoNewUser(): AppUser
    {
        $newUser = new AppUser(
            'chris',
            'holland'
        );
        $this->userRepo->saveUser($newUser);
        /** @var AppUser $retrievedUser */
        $retrievedUser = $this->userRepo->byId($newUser->getId());
        self::assertTrue($retrievedUser->is($newUser));

        return $retrievedUser;
    }

    /**
     * @return AppUser
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    protected function getRepoNewPassenger(): AppUser
    {
        $newUser = $this->getRepoNewUser();
        $newUser->assignRole(
            $this->userRepo->getRoleReference(
                AppRole::passenger()
            )
        );

        $this->userRepo->saveUser($newUser);

        return $newUser;
    }

    /**
     * @return AppUser
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    protected function getRepoNewDriver(): AppUser
    {
        $newUser = $this->getRepoNewUser();
        $newUser->assignRole(
            $this->userRepo->getRoleReference(
                AppRole::driver()
            )
        );
        $this->userRepo->saveUser($newUser);
        return $newUser;
    }

    /**
     * @return AppLocation
     * @throws NonUniqueResultException
     */
    protected function getHomeLocation(): AppLocation
    {
        $retrievedLocation = $this->locationRepo->getOrCreateLocation(
            self::HOME_LOCATION_LAT,
            self::HOME_LOCATION_LONG
        );

        return $retrievedLocation;
    }
}
