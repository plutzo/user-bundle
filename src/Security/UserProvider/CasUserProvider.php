<?php
/**
 * Created by PhpStorm.
 * User: em
 * Date: 11.07.18
 * Time: 11:06
 */

namespace Marlinc\UserBundle\Security\UserProvider;


use Marlinc\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class CasUserProvider extends AbstractUserProvider
{
    /**
     * @inheritDoc
     */
    public function loadUserByUsername($username)
    {
        $user = $this->userManager->findUserBy(['casLogin' => $username]);

        if ($user instanceof UserInterface) {
            return $user;
        }

        throw new UsernameNotFoundException();
    }
}