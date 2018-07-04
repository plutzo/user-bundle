<?php

declare(strict_types=1);

namespace Marlinc\UserBundle\Model;

use Sonata\CoreBundle\Model\PageableManagerInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
interface UserManagerInterface extends PageableManagerInterface
{
    /**
     * Returns the user's fully qualified class name.
     *
     * @return string
     */
    public function getClass(): string;

    /**
     * Creates an empty user instance.
     *
     * @return UserInterface
     */
    public function createUser(): UserInterface;

    /**
     * Reloads a user.
     *
     * @param UserInterface $user
     * @return UserManagerInterface
     */
    public function reloadUser(UserInterface $user): UserManagerInterface;

    /**
     * Updates a user.
     *
     * @param UserInterface $user
     * @return UserManagerInterface
     */
    public function updateUser(UserInterface $user): UserManagerInterface;

    /**
     * Deletes a user.
     *
     * @param UserInterface $user
     * @return UserManagerInterface
     */
    public function deleteUser(UserInterface $user): UserManagerInterface;

    /**
     * Updates the canonical username and email fields for a user.
     *
     * @param UserInterface $user
     * @return UserManagerInterface
     */
    public function updateCanonicalFields(UserInterface $user): UserManagerInterface;

    /**
     * Updates a user password if a plain password is set.
     *
     * @param UserInterface $user
     * @return UserManagerInterface
     */
    public function updatePassword(UserInterface $user): UserManagerInterface;

    /**
     * Finds one user by the given criteria.
     *
     * @param array $criteria
     *
     * @return UserInterface|null
     */
    public function findUserBy(array $criteria): ?UserInterface;

    /**
     * Finds a user by its email.
     *
     * @param string $email
     *
     * @return UserInterface|null
     */
    public function findUserByEmail(string $email): ?UserInterface;

    /**
     * Finds a user by its confirmationToken.
     *
     * @param string $token
     *
     * @return UserInterface|null
     */
    public function findUserByConfirmationToken(string $token): ?UserInterface;

    /**
     * Returns a collection with all user instances.
     *
     * @return UserInterface[]
     */
    public function findUsers(): array;

    /**
     * Alias for the repository method.
     *
     * @param array|null $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return UserInterface[]
     */
    public function findUsersBy(array $criteria = null, array $orderBy = null, $limit = null, $offset = null): array;
}
