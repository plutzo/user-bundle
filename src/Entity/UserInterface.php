<?php

declare(strict_types=1);


namespace Marlinc\UserBundle\Entity;

use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

interface UserInterface extends PasswordAuthenticatedUserInterface, SymfonyUserInterface, EquatableInterface
{
    public const ROLE_DEFAULT = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * @return int|string|null
     */
    public function getId();

    public function getEmail(): ?string;

    public function setEmail(?string $email): self;

    public function getPlainPassword(): ?string;

    public function setPlainPassword(?string $password): self;

    public function setPassword(?string $password): self;

    public function isSuperAdmin(): bool;

    public function setEnabled(bool $enabled): self;

    public function setSuperAdmin(bool $boolean): self;

    public function setLastLogin(?\DateTimeInterface $time = null): self;

    public function hasRole(string $role): bool;

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): self;

    public function addRole(string $role): self;

    public function removeRole(string $role): self;

    public function isEnabled(): bool;

    public function setCreatedAt(?\DateTimeInterface $createdAt = null): self;

    public function getCreatedAt(): ?\DateTimeInterface;

    public function setUpdatedAt(?\DateTimeInterface $updatedAt = null): self;

    public function getUpdatedAt(): ?\DateTimeInterface;

}
