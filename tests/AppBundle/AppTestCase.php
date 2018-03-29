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

    protected function setUp()
    {
        parent::setUp();
        self::bootKernel();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->userManager = new FakeUserManager($this->em());
        $this->setUpEntityManager();
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
}
