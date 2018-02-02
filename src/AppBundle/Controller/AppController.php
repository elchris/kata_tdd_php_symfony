<?php


namespace AppBundle\Controller;

use AppBundle\Entity\AppUser;
use AppBundle\Repository\LocationRepository;
use AppBundle\Repository\RideEventRepository;
use AppBundle\Repository\RideRepository;
use AppBundle\Repository\UserRepository;
use AppBundle\Service\LocationService;
use AppBundle\Service\RideService;
use AppBundle\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\FOSRestController;
use Ramsey\Uuid\Uuid;

class AppController extends FOSRestController
{
    protected function user()
    {
        return new UserService(new UserRepository($this->em()));
    }

    protected function ride()
    {
        return new RideService(
            new RideRepository($this->em()),
            new RideEventRepository($this->em()),
            new LocationRepository($this->em())
        );
    }

    protected function location()
    {
        return new LocationService(
            new LocationRepository($this->em())
        );
    }

    /**
     * @param string $id
     * @return Uuid
     */
    protected function id(string $id)
    {
        /** @var Uuid $uuid */
        $uuid = Uuid::fromString($id);
        return $uuid;
    }

    /**
     * @return EntityManagerInterface
     */
    private function em()
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getDoctrine()->getManager();
        return $em;
    }

    /**
     * @param string $id
     * @return AppUser|mixed
     * @throws \AppBundle\Exception\UserNotFoundException
     */
    protected function getUserById(string $id): AppUser
    {
        return $this->user()->getUserById($this->id($id));
    }
}
