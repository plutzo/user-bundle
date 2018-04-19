<?php

namespace Marlinc\UserBundle\Security;

use Doctrine\ORM\EntityManager;
use Marlinc\UserBundle\Entity\User;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\DBAL\Connection;

class Marlinc1UserProvider implements UserProviderInterface
{
    protected $em;
    private $db;
  
    public function __construct(EntityManager $em, Connection $dbalConnection)
    {
        $this->db = $dbalConnection;
        $this->em = $em;
    }

    public function getUsernameForApiKey($token)
    {
        $marlincuser = false;

        if($token !== null and $token !== '') {
            $query = "SELECT * FROM module_hztools_apikey AS mha 
                          LEFT JOIN core_users AS cu ON cu.id=mha.userID 
                              WHERE cu.active=1 AND mha.token=?";

            if(($stmt = $this->db->prepare($query)) !== false) {
                $stmt->bindValue(1,$token);
                $stmt->execute();
                $user = $stmt->fetchall();

                if($user !== false and count($user) > 0) {
                    $marlincuser = $user[0]['login'];
                }
            }
        }
    
        if($marlincuser !== false) {
            return $marlincuser;
        }
        else {
            throw new UsernameNotFoundException(
                sprintf('User token %s does not exist or has timed out.', $token)
            );
        }
    }

    public function loadUserByUsername($username)
    {
        $user = $this->em
            ->getRepository('MarlincUserBundle:User')
            ->findOneBy([
                'username' => $username
            ]);

        // TODO: Create basic user if none exists.

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }
        $id = $user->getId();

        return $user;
    }

    public function supportsClass($class)
    {
        return $class === 'Marlinc\UserBundle\Entity\User';
    }
}

?>