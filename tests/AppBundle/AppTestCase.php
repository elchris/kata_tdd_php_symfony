<?php

namespace Tests\AppBundle;

use Tests\AppBundle\Production\LocationApi;
use Tests\AppBundle\Production\RideApi;
use Tests\AppBundle\Production\UserApi;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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

    protected function setUp()
    {
        parent::setUp();
        self::bootKernel();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
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
     * @return UserApi
     */
    protected function user()
    {
        if (is_null($this->userApi)) {
            $this->userApi = new UserApi($this->em());
        }
        return $this->userApi;
    }

    /**
     * @return LocationApi
     */
    protected function location()
    {
        if (is_null($this->locationApi)) {
            $this->locationApi = new LocationApi($this->em());
        }
        return $this->locationApi;
    }

    /**
     * @return RideApi
     */
    protected function ride()
    {
        if (is_null($this->rideApi)) {
            $this->rideApi = new RideApi(
                $this->em(),
                $this->user(),
                $this->location()
            );
        }
        return $this->rideApi;
    }
}
