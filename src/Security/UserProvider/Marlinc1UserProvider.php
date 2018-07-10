<?php

namespace Marlinc\UserBundle\Security\UserProvider;

use Doctrine\ORM\EntityManager;
use Marlinc\UserBundle\Entity\User;
use Marlinc\UserBundle\Model\UserManagerInterface;
use Marlinc\UserBundle\Manager\Marlinc1UserLoader;
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
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em, UserManagerInterface $userManager, Marlinc1UserLoader $userLoader)
    {
        parent::__construct($userManager);

        $this->em = $em;
        $this->userLoader = $userLoader;
    }

    public function loadUserByUsername($username)
    {
        $credentials = $this->userLoader->findUserByLogin($username);
        $user = false;

        if ($credentials !== null) {
            $user = User::createFromLegacyAccount($credentials);

            $this->em->persist($user);
            $this->em->flush();

            // TODO: Emit event -> Add flash message, assign to client
        }

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        return $user;
    }

}
