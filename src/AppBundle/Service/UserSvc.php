<?php

namespace AppBundle\Service;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Ramsey\Uuid\Uuid;

class UserSvc
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * UserSvc constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(string $first, string $last) : AppUser
    {
        $newUser = new AppUser($first, $last);
        return $this->userRepository->saveUser($newUser);
    }

    /**
     * @param Uuid $id
     * @return AppUser
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function byId(Uuid $id) : AppUser
    {
        return $this->userRepository->byId($id);
    }

    public function assignRoleToUser(AppUser $user, AppRole $role) : AppUser
    {
        $user->assignRole(
            $this->userRepository->getRoleReference(
                $role
            )
        );
        $this->userRepository->saveUser($user);
        return $user;
    }
}
