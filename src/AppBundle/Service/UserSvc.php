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
    public function register(string $first, string $last) : AppUser
    {
        $newUser = new AppUser($first, $last);
        return $this->userRepository->saveAndGet($newUser);
    }

    public function byId(Uuid $id)
    {
        return $this->userRepository->byId($id);
    }
}
