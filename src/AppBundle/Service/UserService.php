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

    public function newUser($firstName, $lastName)
    {
        $newUser = new AppUser($firstName, $lastName);
        $this->userRepository->save($newUser);
        return $newUser;
    }

    /**
     * @param int $userId
     * @return AppUser
     */
    public function getUserById($userId)
    {
        return $this->userRepository->getUserById($userId);
    }
}
