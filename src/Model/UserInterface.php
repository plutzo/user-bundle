<?php

declare(strict_types=1);

namespace Marlinc\UserBundle\Model;

use Marlinc\UserBundle\Entity\Person;
use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;

interface UserInterface extends BaseUserInterface
{
    const ROLE_DEFAULT = 'ROLE_USER';

    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * Returns the user unique id.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Checks whether the user is enabled.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a DisabledException and prevent login.
     *
     * @return bool true if the user is enabled, false otherwise
     *
     * @see DisabledException
     */
    public function isEnabled(): bool;

    /**
     * Tells if the the given user has the super admin role.
     *
     * @return bool
     */
    public function isSuperAdmin(): bool;

    /**
     * Checks whether the password reset request has expired.
     *
     * @param int $ttl Requests older than this many seconds will be considered expired
     *
     * @return bool
     */
    public function isPasswordRequestNonExpired(int $ttl): bool;

    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the AuthorizationChecker, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $authorizationChecker->isGranted('ROLE_USER');
     *
     * @param string $role
     *
     * @return bool
     */
    public function hasRole(string $role): bool;

    /**
     * Sets the roles of the user.
     *
     * This overwrites any previous roles.
     *
     * @param array $roles
     *
     * @return static
     */
    public function setRoles(array $roles): UserInterface;

    /**
     * Adds a role to the user.
     *
     * @param string $role
     *
     * @return static
     */
    public function addRole(string$role): UserInterface;

    /**
     * Removes a role to the user.
     *
     * @param string $role
     *
     * @return static
     */
    public function removeRole(string $role): UserInterface;

    /**
     * Gets email.
     *
     * @return string
     */
    public function getEmail(): string;

    /**
     * Sets the email.
     *
     * @param string $email
     *
     * @return static
     */
    public function setEmail(string $email): UserInterface;

    /**
     * Gets the plain password.
     *
     * @return string
     */
    public function getPlainPassword(): ?string;

    /**
     * Sets the plain password.
     *
     * @param string $password
     *
     * @return static
     */
    public function setPlainPassword(string $password): UserInterface;

    /**
     * Sets the hashed password.
     *
     * @param string $password
     *
     * @return static
     */
    public function setPassword(string $password): UserInterface;

    /**
     * Sets the super admin status.
     *
     * @param bool $boolean
     *
     * @return static
     */
    public function setSuperAdmin($boolean): UserInterface;

    /**
     * @param bool $boolean
     *
     * @return static
     */
    public function setEnabled($boolean): UserInterface;

    /**
     * Sets the last login time.
     *
     * @param \DateTime|null $time
     *
     * @return static
     */
    public function setLastLogin(\DateTime $time = null): UserInterface;

    /**
     * Sets the confirmation token.
     *
     * @param string|null $confirmationToken
     *
     * @return static
     */
    public function setConfirmationToken($confirmationToken): UserInterface;

    /**
     * Gets the confirmation token.
     *
     * @return string|null
     */
    public function getConfirmationToken(): ?string;

    /**
     * Sets the two-step verification code.
     *
     * @param string $twoStepVerificationCode
     *
     * @return UserInterface
     */
    public function setTwoStepVerificationCode($twoStepVerificationCode): UserInterface;

    /**
     * Returns the two-step verification code.
     *
     * @return string|null
     */
    public function getTwoStepVerificationCode(): ?string;

    /**
     * @param Person $person
     *
     * @return UserInterface
     */
    public function setPerson(Person $person): UserInterface;

    /**
     * @return Person
     */
    public function getPerson(): Person;

    /**
     * @param string $locale
     *
     * @return UserInterface
     */
    public function setLocale($locale): UserInterface;

    /**
     * @return string|null
     */
    public function getLocale(): ?string;

    /**
     * @param string $timezone
     *
     * @return UserInterface
     */
    public function setTimezone($timezone): UserInterface;

    /**
     * @return string|null
     */
    public function getTimezone(): ?string;
}
