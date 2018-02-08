<?php


namespace AppBundle\Controller;

use AppBundle\Entity\AppUser;
use AppBundle\Exception\UserNotFoundException;
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
    /**
     * @return UserService
     */
    protected function user() : UserService
    {
        return new UserService(new UserRepository($this->em()));
    }

    /**
     * @return RideService
     */
    protected function ride() : RideService
    {
        return new RideService(
            new RideRepository($this->em()),
            new RideEventRepository($this->em()),
            new LocationRepository($this->em())
        );
    }

    /**
     * @return LocationService
     */
    protected function location() : LocationService
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
     * @return AppUser
     * @throws UserNotFoundException
     */
    protected function getUserById(string $id): AppUser
    {
        return $this->user()->getUserById($this->id($id));
    }
}
