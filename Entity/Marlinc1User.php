<?php

namespace Marlinc\UserBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

class Marlinc1User implements UserInterface, EquatableInterface
{
    public $username;
    public $password;
    public $salt;
    public $roles;
    public $userID;
    public $userdata;

    public function __construct($userID, $username, $password, $salt, $roles, $userdata, $preferences)
    {
        $this->userID = $userID;
        $this->username = $username;
        $this->password = $password;
        $this->salt = $salt;
        $this->roles = $roles;
        $this->userdata = $userdata;
        $this->preferences = $preferences;
    }

    public function getUserID()
    {
        return $this->userID;
    }
    
    public function getRoles()
    {
        return $this->roles;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getUserData()
    {
        return $this->userdata;
    }
    
    public function getPreferences()
    {
        return $this->preferences;
    }
    
    public function eraseCredentials()
    {
    }

    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof Marlinc1User)
        {
            return false;
        }

        if ($this->password !== $user->getPassword())
        {
            return false;
        }

        if ($this->salt !== $user->getSalt())
        {
            return false;
        }

        if ($this->username !== $user->getUsername())
        {
            return false;
        }

        return true;
    }
}
?>