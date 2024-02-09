<?php

namespace App\Security;

use App\Entity\Account\User;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{

    public function checkPreAuth(UserInterface $user): void
    {
        if(!$user instanceof User) {
            return;
        }

        if(empty($user->getStatus())){
            throw new DisabledException('Deactivated user!');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if(!$user instanceof User) {
            return;
        }
    }
}