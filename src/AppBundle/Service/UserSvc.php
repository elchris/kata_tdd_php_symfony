<?php

namespace AppBundle\Service;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;
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

    public function newUser(string $first, string $last) : AppUser
    {
        $newUser = new AppUser($first, $last);
        $this->userRepository->saveUser($newUser);
        return $newUser;
    }

    public function byId(Uuid $id)
    {
        return $this->userRepository->getById($id);
    }

    public function assignRole(AppUser $user, AppRole $role)
    {
        $user->assignRole(
            $this->userRepository->getRole($role)
        );
        $this->userRepository->saveUser($user);
        return $user;
    }
}
