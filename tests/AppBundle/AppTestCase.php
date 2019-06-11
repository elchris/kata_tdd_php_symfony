<?php

namespace Tests\AppBundle;

use AppBundle\Entity\AppRole;
use AppBundle\Repository\DoctrineUserRepository;
use AppBundle\Repository\UserRepositoryInterface;
use AppBundle\Service\UserSvc;
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
     * @var UserRepositoryInterface
     */
    protected $userRepository;
    /**
     * @var UserSvc
     */
    protected $userService;

    protected function setUp()
    {
        parent::setUp();
        self::bootKernel();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $this->userManager = new FakeUserManager($this->em());
        $this->setUpEntityManager();

        $this->userRepository = new DoctrineUserRepository($this->em());
        $this->userService = new UserSvc(
            $this->userRepository
        );

        $this->save(AppRole::passenger());
        $this->save(AppRole::driver());
    }

    protected function em(): EntityManagerInterface
    {
        return $this->em;
    }

    protected function save($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
        return $entity;
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
}
