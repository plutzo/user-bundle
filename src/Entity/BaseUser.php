<?php

declare(strict_types=1);


namespace Marlinc\UserBundle\Entity;

use Marlinc\UserBundle\Util\EmailCanonicalizer;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

class BaseUser implements UserInterface
{
    /**
     * @var int|string|null
     */
    protected $id;

    protected ?string $email = null;

    protected bool $enabled = false;

    protected ?string $password = null;

    protected ?string $plainPassword = null;

    protected ?\DateTimeInterface $lastLogin = null;

    /**
     * @var string[]
     */
    protected array $roles = [];

    protected ?\DateTimeInterface $createdAt = null;

    protected ?\DateTimeInterface $updatedAt = null;

    public function __toString(): string
    {
        return $this->getEmail() ?? '-';
    }

    /**
     * @return mixed[]
     */
    public function __serialize(): array
    {
        return [
            $this->password,
            $this->enabled,
            $this->id,
            $this->email,
        ];
    }

    /**
     * @param mixed[] $data
     */
    public function __unserialize(array $data): void
    {
        [
            $this->password,
            $this->enabled,
            $this->id,
            $this->email,
        ] = $data;
    }

    public function addRole(string $role): void
    {
        $role = strtoupper($role);

        if ($role === static::ROLE_DEFAULT) {
            return;
        }

        if (!\in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    public function getUserIdentifier(): string
    {
        return $this->email ?? '-';
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;

        return array_values(array_unique($roles));
    }

    public function hasRole(string $role): bool
    {
        return \in_array(strtoupper($role), $this->getRoles(), true);
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(static::ROLE_SUPER_ADMIN);
    }

    public function removeRole(string $role): void
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function setSuperAdmin(bool $boolean): void
    {
        if (true === $boolean) {
            $this->addRole(static::ROLE_SUPER_ADMIN);
        } else {
            $this->removeRole(static::ROLE_SUPER_ADMIN);
        }
    }

    public function setPlainPassword(?string $password): void
    {
        $this->plainPassword = $password;
    }

    public function setLastLogin(?\DateTimeInterface $time = null): void
    {
        $this->lastLogin = $time;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }
    }

    public function isEqualTo(SymfonyUserInterface $user): bool
    {
        if (!$user instanceof self) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        return true;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt = null): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt = null): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function prePersist(): void
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->email = EmailCanonicalizer::canonicalize($this->email);
    }

    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTime();
        $this->email = EmailCanonicalizer::canonicalize($this->email);
    }
}
