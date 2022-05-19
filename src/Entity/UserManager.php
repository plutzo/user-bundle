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
use Marlinc\UserBundle\Util\EmailCanonicalizer;
use Sonata\Doctrine\Entity\BaseEntityManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


/**
 * @author Hugo Briand <briand@ekino.com>
 *
 */
final class UserManager extends BaseEntityManager implements UserManagerInterface
{
    /**
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $userPasswordHasher;

    /**
     *
     * @param UserPasswordHasherInterface $userPasswordHasher
     */
    public function __construct(
        string $class,
        ManagerRegistry $registry,
        UserPasswordHasherInterface $userPasswordHasher
    ) {
        parent::__construct($class, $registry);

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

    public function findUserByEmail(string $email): ?UserInterface
    {
        return $this->findOneBy([
            'email' => EmailCanonicalizer::canonicalize($email),
        ]);
    }

    public function findUserByConfirmationToken(string $token): ?UserInterface
    {
        return $this->findOneBy(['confirmationToken' => $token]);
    }
}
