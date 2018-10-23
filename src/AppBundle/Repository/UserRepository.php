<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AppRole;
use AppBundle\Entity\AppUser;
use AppBundle\Exception\DuplicateRoleAssignmentException;
use AppBundle\Exception\UserNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Ramsey\Uuid\Uuid;

class UserRepository extends AppRepository implements UserRepositoryInterface
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserManagerInterface $userManager = null
    ) {
        parent::__construct($entityManager);
        $this->userManager = $userManager;
    }

    /**
     * @param Uuid $userId
     * @return AppUser
     * @throws UserNotFoundException
     */
    public function getUserById(Uuid $userId): AppUser
    {
        try {
            return $this->em->createQuery(
                'select u from E:AppUser u where u.id = :userId'
            )
                ->setParameter('userId', $userId)
                ->getSingleResult();
        } catch (\Exception $e) {
            throw new UserNotFoundException();
        }
    }

    /**
     * @param AppUser $user
     * @param AppRole $role
     * @throws DuplicateRoleAssignmentException
     */
    public function assignRoleToUser(AppUser $user, AppRole $role): void
    {
        if ($user->userHasRole($role)) {
            throw new DuplicateRoleAssignmentException();
        }
        $role = $this->getRoleReference($role);
        $user->assignRole($role);
        $this->save($user);
    }

    /**
     * @param AppUser $passedUser
     * @return AppUser
     */
    public function saveNewUser(AppUser $passedUser): AppUser
    {
        /** @var AppUser $user */
        $user = $this->userManager->createUser();
        $user->setFirstName($passedUser->getFirstName());
        $user->setLastName($passedUser->getLastName());
        $user->setUsername($passedUser->getUsername());
        $user->setEmail($passedUser->getEmail());
        $user->setPlainPassword($passedUser->getPlainPassword());
        $user->setEnabled(true);
        $this->userManager->updateUser($user);
        return $user;
    }

    /**
     * @param AppRole $role
     * @return null | AppRole
     */
    private function getRoleReference(AppRole $role): AppRole
    {
        /** @var AppRole $role */
        $role = $this->em->getRepository(AppRole::class)->findOneBy(
            [
                'id' => $role->getId()
            ]
        );
        return $role;
    }
}
