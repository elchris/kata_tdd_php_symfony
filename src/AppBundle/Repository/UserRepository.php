<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AppUser;
use Doctrine\ORM\EntityManagerInterface;

class UserRepository extends AppRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, AppUser::class);
    }

    /**
     * @param integer $id
     * @return AppUser
     */
    public function getUserById($id)
    {
        /** @var AppUser $user */
        $user = $this->find($id);
        return $user;
    }
}
