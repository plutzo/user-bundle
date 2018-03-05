<?php
/**
 * Created by PhpStorm.
 * User: elias
 * Date: 23.03.17
 * Time: 15:49
 */

namespace Marlinc\UserBundle\Event;

use Marlinc\UserBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Acl\Model\ObjectIdentityInterface;

class UserAclUpdateEvent extends Event
{
    const NAME = 'marlinc.user.acl.update';

    /**
     * @var User
     */
    private $user;

    /**
     * @var array
     */
    private $objectIdentities;

    /**
     * @var array
     */
    private $masks;

    /**
     * UserAclUpdateEvent constructor.
     * @param $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->objectIdentities = [];
        $this->masks = [];
    }

    /**
     * @return array
     */
    public function getObjectIdentities(): array
    {
        return $this->objectIdentities;
    }

    /**
     * @param array $objectIdentities
     * @return UserAclUpdateEvent
     */
    public function setObjectIdentities(array $objectIdentities): UserAclUpdateEvent
    {
        $this->objectIdentities = $objectIdentities;
        return $this;
    }

    /**
     * @return array
     */
    public function getMasks(): array
    {
        return $this->masks;
    }

    /**
     * @param array $masks
     * @return UserAclUpdateEvent
     */
    public function setMasks(array $masks): UserAclUpdateEvent
    {
        $this->masks = $masks;
        return $this;
    }

    public function addMask($mask) {
        $this->masks[] = $mask;
        return $this;
    }

    public function addObjectIdentity(ObjectIdentityInterface $objectIdentity) {
        $this->objectIdentities[] = $objectIdentity;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}