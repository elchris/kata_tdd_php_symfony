<?php

namespace AppBundle\Service;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
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

    /**
     * @param string $first
     * @param string $last
     * @return AppUser
     */
    public function newUser(string $first, string $last)
    {
        $newUser = new AppUser($first, $last);
        $this->userRepository->saveUser($newUser);
        return $newUser;
    }

    /**
     * @param Uuid $id
     * @return AppUser
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getById(Uuid $id)
    {
        return $this->userRepository->getById($id);
    }

    /**
     * @param AppUser $user
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function makeUserPassenger(AppUser $user)
    {
        $this->userRepository->assignRoleToUser(
            $user,
            AppRole::passenger()
        );
    }

    /**
     * @param AppUser $user
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function makeUserDriver(AppUser $user)
    {
        $this->userRepository->assignRoleToUser(
            $user,
            AppRole::driver()
        );
    }
}
