<?php

namespace App\Twig\Runtime;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Twig\Extension\RuntimeExtensionInterface;

class AuthRuntime extends AbstractController implements RuntimeExtensionInterface
{
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function getUsername() : string
    {
        $userPassport = $this->getUser();
        /** @var User $user */
        $user = $this->managerRegistry->getRepository(User::class)
            ->findOneBy(['email' => $userPassport->getUserIdentifier()]);

        if (!is_null($user)) {
            return $user->getUsername();
        }

        return "";
    }

    public function isAuthorized() : bool
    {
        $user = $this->getUser();

        if (!is_null($user)) {
            return true;
        }

        return false;
    }
}
