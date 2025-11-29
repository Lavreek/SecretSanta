<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class UserHandler
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function getUserByEmail($email) : ?User
    {
        return $this->registry->getRepository(User::class)
            ->findOneBy(['email' => $email]);
    }
}