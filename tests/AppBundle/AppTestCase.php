<?php


namespace Tests\AppBundle;

use Tests\AppBundle\Production\LocationApi;
use Tests\AppBundle\Production\RideApi;
use Tests\AppBundle\Production\UserApi;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\RideEventType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AppTestCase extends WebTestCase
{
    /** @var RideApi */
    private $rideApi;

    /** @var  EntityManagerInterface */
    private $em;

    /** @var RideEventType $requestedType */
    protected $requestedType;

    /** @var RideEventType $acceptedType */
    protected $acceptedType;

    /** @var RideEventType $inProgressType */
    protected $inProgressType;

    /** @var RideEventType $cancelledType */
    protected $cancelledType;

    /** @var RideEventType $completedType */
    protected $completedType;

    /** @var RideEventType $rejectedType */
    protected $rejectedType;

    protected function setUp()
    {
        parent::setUp();
        self::bootKernel();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->setUpEntityManager();

        $this->bootStrapAppRoles();
        $this->ride()->bootStrapRideEventTypes();
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
        return new UserApi($this->em());
    }

    /**
     * @return LocationApi
     */
    protected function location()
    {
        return new LocationApi($this->em());
    }

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

    private function bootStrapAppRoles(): void
    {
        $this->save(AppRole::driver());
        $this->save(AppRole::passenger());
    }
}
