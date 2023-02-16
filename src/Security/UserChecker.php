<?php

namespace Marlinc\UserBundle\Security;

use Marlinc\UserBundle\Entity\BaseUser;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof BaseUser) {
            return;
        }

        if (!$user->isEnabled()) {
            throw new CustomUserMessageAccountStatusException('Your user account is not active.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Nothing to do here
    }
}
