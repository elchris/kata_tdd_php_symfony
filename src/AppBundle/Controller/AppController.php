<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Ride;
use AppBundle\Repository\LocationRepository;
use AppBundle\Repository\RideRepository;
use AppBundle\Repository\UserRepository;
use AppBundle\Service\LocationService;
use AppBundle\Service\RideService;
use AppBundle\Service\UserSvc;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\UserBundle\Model\UserManagerInterface;
use phpDocumentor\Reflection\Location;
use Ramsey\Uuid\Uuid;

class AppController extends AbstractFOSRestController
{
    protected function getUserManager() : UserManagerInterface
    {
        return $this->container->get('fos_user.user_manager.public');
    }

    protected function userService() : UserSvc
    {
        return new UserSvc(
            new UserRepository($this->em())
        );
    }

    protected function locationService() : LocationService
    {
        return new LocationService(
            new LocationRepository($this->em())
        );
    }

    protected function rideService() : RideService
    {
        return new RideService(
            new RideRepository($this->em())
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
}
