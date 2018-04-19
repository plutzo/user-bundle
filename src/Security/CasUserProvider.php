<?php

namespace Marlinc\UserBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\EntityManager;

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
        return 'Marlinc\UserBundle\Entity\User' === $class;
    }
}
