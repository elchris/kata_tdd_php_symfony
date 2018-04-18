<?php


namespace AppBundle\Service;

use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;
use Ramsey\Uuid\Uuid;

class UserService
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function newUser(string $first, string $last) : AppUser
    {
        $newUser = new AppUser($first, $last);
        $this->userRepository->saveUser($newUser);
        return $newUser;
    }

    /**
     * @param Uuid $id
     * @return AppUser
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getById(Uuid $id)
    {
        return $this->userRepository->getById($id);
    }
}
