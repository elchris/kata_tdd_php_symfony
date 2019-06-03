<?php


namespace AppBundle\Controller;

use AppBundle\Repository\UserRepository;
use AppBundle\Service\UserSvc;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\UserBundle\Model\UserManagerInterface;
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
