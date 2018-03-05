<?php

namespace Marlinc\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="cas_users")
 * @ORM\HasLifecycleCallbacks()
 */
class CasUser
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=10)
     */    
    private $sapnr;

    /**
     * @ORM\Column(type="string", length=64)
     */    
    private $username;

    /**
     * @ORM\Column(type="integer")
     */   
    private $usernumber;

    /**
     * @ORM\Column(type="string", length=190)
     */
    private $fullname;

    /**
     * @ORM\Column(type="string", length=64)
     */    
    private $channel;

    /**
     * @ORM\Column(type="boolean")
     */    
    private $entrepreneur;
    
    /**
     * @ORM\Column(type="datetime")
     */    
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="Marlinc\UserBundle\Entity\User",cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    public function getId()
    {
        return $this->id;
    }

    public function getSapnr()
    {
        return $this->sapnr;
    }

    public function setSapnr($value)
    {
        $this->sapnr = $value;
        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($value)
    {
        $this->username = $value;
        return $this;
    }

    public function getUsernumber()
    {
        return $this->usernumber;
    }

    public function setUsernumber($value)
    {
        $this->usernumber  = $value;
        return $this;
    }

    public function getFullname()
    {
        return $this->fullname;
    }

    public function setFullname($value)
    {
        $this->fullname  = $value;
        return $this;
    }

    public function getChannel()
    {
        return $this->channel;
    }

    public function setChannel($value)
    {
        $this->channel = $value;
        return $this;
    }

    public function getEntrepreneur()
    {
        return $this->entrepreneur;
    }

    public function setEntrepreneur($value)
    {
        $this->entrepreneur = $value;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return CasUser
     */
    public function setUser(User $user): CasUser
    {
        $this->user = $user;
        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime();
    }
}


?>