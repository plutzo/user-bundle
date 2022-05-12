<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Marlinc\UserBundle\Entity;

use Doctrine\Persistence\ManagerRegistry;
use Sonata\Doctrine\Entity\BaseEntityManager;
use Marlinc\UserBundle\Entity\UserInterface;
use Marlinc\UserBundle\Entity\UserManagerInterface;
use Marlinc\UserBundle\Util\CanonicalFieldsUpdaterInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 *
 * @phpstan-extends BaseEntityManager<UserInterface>
 */
final class UserManager extends BaseEntityManager implements UserManagerInterface
{
    private CanonicalFieldsUpdaterInterface $canonicalFieldsUpdater;

    /**
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $userPasswordHasher;

    /**
     *
     * @phpstan-param class-string<UserInterface> $class
     *
     * @param UserPasswordHasherInterface $userPasswordHasher
     */
    public function __construct(
        string $class,
        ManagerRegistry $registry,
        CanonicalFieldsUpdaterInterface $canonicalFieldsUpdater,
        UserPasswordHasherInterface $userPasswordHasher
    ) {
        parent::__construct($class, $registry);

        $this->canonicalFieldsUpdater = $canonicalFieldsUpdater;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     * @psalm-suppress UndefinedDocblockClass
     */
    public function updatePassword(UserInterface $user): void
    {
        $plainPassword = $user->getPlainPassword();

        if (null === $plainPassword) {
            return;
        }
        
        $password = $this->userPasswordHasher->hashPassword($user, $plainPassword);

        $user->setPassword($password);
        $user->eraseCredentials();
    }

    public function findUserByUsername(string $username): ?UserInterface
    {
        return $this->findOneBy([
            'usernameCanonical' => $this->canonicalFieldsUpdater->canonicalizeUsername($username),
        ]);
    }

    public function findUserByEmail(string $email): ?UserInterface
    {
        return $this->findOneBy([
            'emailCanonical' => $this->canonicalFieldsUpdater->canonicalizeEmail($email),
        ]);
    }

    public function findUserByUsernameOrEmail(string $usernameOrEmail): ?UserInterface
    {
        if (1 === preg_match('/^.+\@\S+\.\S+$/', $usernameOrEmail)) {
            $user = $this->findUserByEmail($usernameOrEmail);
            if (null !== $user) {
                return $user;
            }
        }

        return $this->findUserByUsername($usernameOrEmail);
    }

    public function findUserByConfirmationToken(string $token): ?UserInterface
    {
        return $this->findOneBy(['confirmationToken' => $token]);
    }
}
