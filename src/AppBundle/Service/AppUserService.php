<?php


namespace AppBundle\Service;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;
use Ramsey\Uuid\Uuid;

class AppUserService
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
     * @throws \Exception
     */
    public function registerUser(string $first, string $last) : AppUser
    {
        $newUser = new AppUser($first, $last);
        $this->userRepository->saveUser($newUser);
        return $newUser;
    }

    public function byId(Uuid $userId) : AppUser
    {
        return $this->userRepository->byId($userId);
    }

    public function assignRoleToUser(AppUser $user, AppRole $role) : AppUser
    {
        $referencedRole = $this->userRepository->getRoleReference($role);
        $user->assignRole($referencedRole);

        $this->userRepository->saveUser($user);
        return $user;
    }
}
