<?php

namespace AppBundle\Service;

use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepository;

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
     * @param $first
     * @param $last
     * @return AppUser
     */
    public function newUser($first, $last)
    {
        $newUser = new AppUser($first, $last);
        $this->userRepository->saveUser($newUser);
        return $newUser;
    }
}
