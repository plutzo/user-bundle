<?php

declare(strict_types=1);


namespace Marlinc\UserBundle\Entity;

use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

interface UserInterface extends PasswordAuthenticatedUserInterface, SymfonyUserInterface, EquatableInterface
{
    public const ROLE_DEFAULT = 'ROLE_USER';
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * @return int|string|null
     */
    public function getId();

    public function getEmail(): ?string;

    public function setEmail(?string $email): void;

    public function getPlainPassword(): ?string;

    public function setPlainPassword(?string $password): void;

    public function setPassword(?string $password): void;

    public function isSuperAdmin(): bool;

    public function setEnabled(bool $enabled): void;

    public function setSuperAdmin(bool $boolean): void;

    public function setLastLogin(?\DateTimeInterface $time = null): void;

    public function hasRole(string $role): bool;

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): void;

    public function addRole(string $role): void;

    public function removeRole(string $role): void;

    public function isEnabled(): bool;

    public function setCreatedAt(?\DateTimeInterface $createdAt = null): void;

    public function getCreatedAt(): ?\DateTimeInterface;

    public function setUpdatedAt(?\DateTimeInterface $updatedAt = null): void;

    public function getUpdatedAt(): ?\DateTimeInterface;

}
