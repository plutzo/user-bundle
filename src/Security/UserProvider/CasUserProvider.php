<?php

namespace Marlinc\UserBundle\Security\UserProvider;

use Marlinc\UserBundle\Entity\User;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\EntityManager;

/**
 * Try to load the user data from the GDHS CAS System and migrate it
 * to the Marlinc2 user system.
 */
class CasUserProvider implements UserProviderInterface
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }    
    
    public function getUsernameForApiKey($authUserId)
    {
        // Look up the username based on the token in the database.
        $CasUser = $this->em
            ->getRepository('MarlincUserBundle:CasUser')
            ->findOneBy([
                'username' => base64_decode($authUserId)
            ]);
                  
        if($CasUser->getUsername() != '') {
            return $CasUser->getUser()->getUsername();
        } else {
            return false;
        }
    }

    public function loadUserByUsername($username)
    {
        $user = $this->em
            ->getRepository('MarlincUserBundle:User')
            ->findOneBy([
                'username' => $username
            ]);
        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

    public function supportsClass($class)
    {
        return $class === User::class;
    }
}
