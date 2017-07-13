<?php


namespace AppBundle\Service;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Repository\UserRepositoryInterface;
use AppBundle\RoleLifeCycleException;

class UserService
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct($userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function newUser($firstName, $lastName)
    {
        $this->userRepository->newUser($firstName, $lastName);
    }

    /**
     * @param integer $userId
     * @return AppUser
     */
    public function getUserById($userId)
    {
        return $this->userRepository->getUserById($userId);
    }

    public function assignRoleToUser(AppUser $user, AppRole $role)
    {
        if (!$this->userRepository->isUserInRole($user, $role)) {
            $this->userRepository->assignRoleToUser($user, $role);
        } else {
            throw new RoleLifeCycleException(
                'User: '
                .$user->getFullName()
                .' is already of Role: '
                .$role->getName()
            );
        }
    }

    /**
     * @param AppUser $user
     * @return bool
     */
    public function isUserPassenger(AppUser $user)
    {
        return $this->userRepository->isUserInRole($user, AppRole::asPassenger());
    }

    public function isUserDriver(AppUser $user)
    {
        return $this->userRepository->isUserInRole($user, AppRole::asDriver());
    }
}
