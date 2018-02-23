<?php


namespace Tests\AppBundle\Production;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Exception\DuplicateRoleAssignmentException;
use AppBundle\Exception\UnauthorizedOperationException;
use AppBundle\Exception\UserNotFoundException;
use AppBundle\Repository\UserRepository;
use AppBundle\Repository\UserRepositoryInterface;
use AppBundle\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Tests\AppBundle\User\FakeUser;

class UserApi
{
    /** @var UserService */
    private $userService;

    /** @var UserRepository */
    private $userRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var boolean $authUserIsSet */
    private $authUserIsSet = false;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserManagerInterface $userManager = null
    ) {
        $this->userRepository = new UserRepository(
            $entityManager,
            $userManager
        );
        $this->userService = new UserService(
            $this->userRepository
        );
        $this->entityManager = $entityManager;
    }

    const CLIENT_ID = '1_3bcbxd9e24g0gk4swg0kwgcwg4o8k8g4g888kwc44gcc0gwwk4';
    const CLIENT_SECRET = '4ok2x70rlfokc8g0wws8c8kwcokw80k44sg48goc0ok4w0so0k';

    /**
     * @return UserRepositoryInterface
     */
    public function getRepo()
    {
        return $this->userRepository;
    }

    /**
     * @param $userId
     * @return AppUser
     * @throws UserNotFoundException
     * @throws UnauthorizedOperationException
     */
    public function getServiceUserById($userId)
    {
        $this->authById($userId);
        return $this->userService->getUserById($userId);
    }

    public function getSavedUserWithName($first, $last)
    {
        $fakeUser = new FakeUser($first, $last);
        return $this->userService->newUser(
            $first,
            $last,
            $fakeUser->email,
            $fakeUser->username,
            $fakeUser->password
        );
    }

    /**
     * @return AppUser
     */
    public function getSavedUser()
    {
        return $this->getSavedUserWithName('chris', 'holland');
    }

    /**
     * @return AppUser
     * @throws DuplicateRoleAssignmentException
     * @throws UnauthorizedOperationException
     */
    public function getNewPassenger()
    {
        $passenger = $this->getSavedUser();
        $this->makeUserPassenger($this->getSavedUser());
        return $passenger;
    }

    /**
     * @param AppUser $user
     * @throws DuplicateRoleAssignmentException
     * @throws UnauthorizedOperationException
     */
    public function makeUserPassenger(AppUser $user)
    {
        $this->auth($user);
        $this->userService->makeUserPassenger($user);
    }

    /**
     * @return AppUser
     * @throws DuplicateRoleAssignmentException
     * @throws UserNotFoundException
     * @throws UnauthorizedOperationException
     */
    public function getSavedPassenger(): AppUser
    {
        $ridePassenger = $this->getNewPassenger();
        $savedPassenger = $this->getServiceUserById($ridePassenger->getId());

        return $savedPassenger;
    }

    /**
     * @param AppUser $driver
     * @throws DuplicateRoleAssignmentException
     * @throws UnauthorizedOperationException
     */
    public function makeUserDriver(AppUser $driver)
    {
        $this->auth($driver);
        $this->userService->makeUserDriver($driver);
    }

    /**
     * @return AppUser
     * @throws DuplicateRoleAssignmentException
     * @throws UnauthorizedOperationException
     */
    public function getNewDriver()
    {
        return $this->getNewDriverWithName('new', 'driver');
    }

    /**
     * @param $first
     * @param $last
     * @return AppUser
     * @throws DuplicateRoleAssignmentException
     * @throws UnauthorizedOperationException
     */
    public function getNewDriverWithName($first, $last)
    {
        $driver = $this->getSavedUserWithName($first, $last);
        $this->makeUserDriver($driver);
        return $driver;
    }

    private function saveRole(AppRole $role)
    {
        $this->entityManager->persist($role);
        $this->entityManager->flush();
    }

    public function bootStrapRoles()
    {
        $this->saveRole(AppRole::driver());
        $this->saveRole(AppRole::passenger());
    }

    /**
     * @return UserService
     */
    public function getService() : UserService
    {
        return $this->userService;
    }

    public function setAuthenticatedUser(AppUser $user)
    {
        $this->authUserIsSet = true;
        $this->userService->setAuthenticatedUser($user);
    }

    private function auth(AppUser $user)
    {
        if (! $this->authUserIsSet) {
            $this->userService->setAuthenticatedUser($user);
        }
    }

    /**
     * @param $userId
     * @throws UserNotFoundException
     */
    public function authById($userId): void
    {
        if (! $this->authUserIsSet) {
            $this->userService->setAuthenticatedUser(
                $this->userRepository->getUserById($userId)
            );
        }
    }
}
