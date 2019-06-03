<?php

namespace AppBundle\Service;

use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;
use Exception;
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

    /**
     * @param string $first
     * @param string $last
     * @return AppUser
     * @throws Exception
     */
    public function registerNewUser(string $first, string $last) : AppUser
    {
        $userToRegister = new AppUser($first, $last);
        return $this->userRepository->saveUser($userToRegister);
    }

    public function byId(Uuid $userId)
    {
        return $this->userRepository->byId($userId);
    }
}
