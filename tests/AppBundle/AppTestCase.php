<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppUser;
use FOS\UserBundle\Model\UserManagerInterface;
use Tests\AppBundle\Production\LocationApi;
use Tests\AppBundle\Production\RideApi;
use Tests\AppBundle\Production\UserApi;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\AppBundle\User\FakeUser;
use Tests\AppBundle\User\FakeUserManager;

abstract class AppTestCase extends WebTestCase
{
    /** @var RideApi */
    private $rideApi;

    /** @var LocationApi */
    private $locationApi;

    /** @var UserApi */
    private $userApi;

    /** @var  EntityManagerInterface */
    private $em;

    /** @var UserManagerInterface */
    private $userManager;

    protected function setUp() : void
    {
        parent::setUp();
        self::bootKernel();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->userManager = new FakeUserManager($this->em());
        $this->setUpEntityManager();

        $this->ride()->bootStrapRideEventTypes();
        $this->user()->bootStrapRoles();
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

    /**
     * @param $firstName
     * @param $lastName
     * @return AppUser
     */
    protected function newNamedUser($firstName, $lastName): AppUser
    {
        return (new FakeUser($firstName, $lastName))->toEntity();
    }

    private function setUpEntityManager(): void
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
     * @return UserApi
     */
    protected function user(): UserApi
    {
        if ($this->userApi === null) {
            $this->userApi = new UserApi(
                $this->em(),
                $this->userManager
            );
        }
        return $this->userApi;
    }

    /**
     * @return LocationApi
     */
    protected function location(): LocationApi
    {
        if ($this->locationApi === null) {
            $this->locationApi = new LocationApi($this->em());
        }
        return $this->locationApi;
    }

    /**
     * @return RideApi
     */
    protected function ride(): RideApi
    {
        if ($this->rideApi === null) {
            $this->rideApi = new RideApi(
                $this->em(),
                $this->user(),
                $this->location()
            );
        }
        return $this->rideApi;
    }
}
