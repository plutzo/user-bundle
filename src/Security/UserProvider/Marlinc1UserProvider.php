<?php

namespace Marlinc\UserBundle\Security\UserProvider;

use Marlinc\UserBundle\Entity\User;
use Marlinc\UserBundle\Event\UserEvents;
use Marlinc\UserBundle\Event\UserImportEvent;
use Marlinc\UserBundle\Model\UserManagerInterface;
use Marlinc\UserBundle\Manager\Marlinc1UserLoader;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * Try to load the user data from one of the Marlinc1 databases and migrate it
 * to the Marlinc2 user system.
 */
class Marlinc1UserProvider extends AbstractUserProvider
{
    /**
     * @var Marlinc1UserLoader
     */
    private $userLoader;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * Marlinc1UserProvider constructor.
     * @param UserManagerInterface $userManager
     * @param Marlinc1UserLoader $userLoader
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(UserManagerInterface $userManager, Marlinc1UserLoader $userLoader, EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct($userManager);

        $this->eventDispatcher = $eventDispatcher;
        $this->userLoader = $userLoader;
    }

    public function loadUserByUsername($username)
    {
        $credentials = $this->userLoader->findUserByLogin($username);

        if ($credentials !== null) {
            $user = User::createFromLegacyAccount($credentials);

            $this->userManager->updateUser($user);

            $event = new UserImportEvent($user, $credentials);
            $this->eventDispatcher->dispatch(UserEvents::SECURITY_LEGACY_IMPORT, $event);
            // TODO: Add event listener -> Add flash message, assign to client, force user to reset password

            return $user;
        }

        throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
    }
}
