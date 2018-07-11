<?php
/**
 * Created by PhpStorm.
 * User: em
 * Date: 11.07.18
 * Time: 10:40
 */

namespace Marlinc\UserBundle\Event;


use Marlinc\UserBundle\Model\UserInterface;

class UserImportEvent extends UserEvent
{
    /**
     * @var array
     */
    private $credentials;

    /**
     * UserImportEvent constructor.
     * @param UserInterface $user
     * @param array $credentials
     */
    public function __construct(UserInterface $user, array $credentials)
    {
        parent::__construct($user);

        $this->credentials = $credentials;
    }

    /**
     * @return array
     */
    public function getCredentials(): array
    {
        return $this->credentials;
    }
}