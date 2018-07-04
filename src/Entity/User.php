<?php

namespace Marlinc\UserBundle\Entity;

use Marlinc\EntityBundle\Entity\EntityReference;
use Marlinc\ClientBundle\Entity\Client;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Marlinc\UserBundle\Model\GroupableInterface;
use Marlinc\UserBundle\Model\GroupInterface;
use Marlinc\UserBundle\Model\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class User
 *
 * @ORM\Entity
 * @ORM\Table(name="user_users")
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\Loggable
 * @UniqueEntity("email")
 */
class User extends EntityReference implements UserInterface, GroupableInterface
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $lastLogin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $passwordRequestedAt;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email()
     * @Gedmo\Versioned
     */
    protected $email;

    /**
     * Encrypted password. Must be persisted.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=190)
     * @Gedmo\Versioned
     */
    protected $password;

    /**
     * Plain password. Used for model validation. Must not be persisted.
     *
     * @var string
     */
    protected $plainPassword;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     * @Gedmo\Versioned
     */
    protected $enabled;

    /**
     * If not null, a two step verification with GoogleAuthenticator app will be used on each login.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=190, nullable=true)
     */
    protected $twoStepVerificationCode;

    /**
     * Random string sent to the user email address in order to verify the user account registration.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=190, nullable=true, unique=true)
     */
    protected $confirmationToken;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Marlinc\UserBundle\Entity\Group")
     * @ORM\JoinTable(name="user_users_groups",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected $roles;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=8, nullable=true)
     * @Assert\Locale()
     * @Gedmo\Versioned
     */
    protected $locale;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Gedmo\Versioned
     */
    protected $timezone;

    /**
     * @var Person
     *
     * @ORM\OneToOne(targetEntity="Marlinc\UserBundle\Entity\Person",cascade={"persist"},inversedBy="user")
     */
    protected $person;

    /**
     * @var Client
     *
     * TODO: Migrate reference to client object (person relation).
     * @ORM\ManyToOne(targetEntity="Marlinc\ClientBundle\Entity\Client")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $client;

    public function __construct()
    {
        parent::__construct();

        $this->enabled = false;
        $this->roles = [];
        $this->person = new Person();
    }

    public function __toString()
    {
        return $this->getFullName().' ('.$this->email.')';
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function setEntityLabel()
    {
        $this->setLabel($this->__toString());
    }

    /**
     * @return string
     */
    public function getEntityLabel(): string
    {
        return $this->__toString();
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return Collection
     */
    public function getAllReferencingEntities(): Collection
    {
        $all = new ArrayCollection(
            array_merge($this->getReferencingEntities()->toArray(), $this->person->getReferencingEntities()->toArray())
        );

        foreach ($all as $key => $item) {
            if ($item instanceof User) {
                $all->remove($key);
            }
        }
        return $all;
    }

    /**
     * @param Client $client
     * @return User
     */
    public function setClient(Client $client): User
    {
        $this->client = $client;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addRole(string $role): UserInterface
    {
        $role = strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->getEmail();
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * Gets the last login time.
     *
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmationToken(): string
    {
        return $this->confirmationToken;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        $roles = $this->roles;

        foreach ($this->getGroups() as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }

        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;

        return array_unique($roles);
    }

    /**
     * {@inheritdoc}
     */
    public function hasRole(string $role): bool
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(UserInterface::ROLE_SUPER_ADMIN);
    }

    /**
     * {@inheritdoc}
     */
    public function removeRole(string $role): UserInterface
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail(string $email): UserInterface
    {
        $this->email = $email;
        if ($this->person instanceof Person) {
            $this->person->setEmail($email);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled($boolean): UserInterface
    {
        $this->enabled = (bool) $boolean;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPassword(string $password): UserInterface
    {
        $this->password = $password;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSuperAdmin($boolean): UserInterface
    {
        if (true === $boolean) {
            $this->addRole(static::ROLE_SUPER_ADMIN);
        } else {
            $this->removeRole(static::ROLE_SUPER_ADMIN);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPlainPassword(string $password): UserInterface
    {
        $this->plainPassword = $password;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastLogin(\DateTime $time = null): UserInterface
    {
        $this->lastLogin = $time;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfirmationToken($confirmationToken): UserInterface
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPasswordRequestedAt(\DateTime $date = null)
    {
        $this->passwordRequestedAt = $date;

        return $this;
    }

    /**
     * Gets the timestamp that the user requested a password reset.
     *
     * @return null|\DateTime
     */
    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function isPasswordRequestNonExpired(int $ttl): bool
    {
        return $this->getPasswordRequestedAt() instanceof \DateTime &&
            $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }

    /**
     * {@inheritdoc}
     */
    public function setRoles(array $roles): UserInterface
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroups(): Collection
    {
        return $this->groups ?: $this->groups = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupNames(): array
    {
        $names = [];
        foreach ($this->getGroups() as $group) {
            $names[] = $group->getName();
        }

        return $names;
    }

    /**
     * {@inheritdoc}
     */
    public function hasGroup($name): bool
    {
        return in_array($name, $this->getGroupNames());
    }

    /**
     * {@inheritdoc}
     */
    public function addGroup(GroupInterface $group): GroupableInterface
    {
        if (!$this->getGroups()->contains($group)) {
            $this->getGroups()->add($group);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeGroup(GroupInterface $group): GroupableInterface
    {
        if ($this->getGroups()->contains($group)) {
            $this->getGroups()->removeElement($group);
        }

        return $this;
    }

    public function getFullName()
    {
        return $this->person->getFullName();
    }

    /**
     * @return string|null
     */
    public function getTwoStepVerificationCode(): ?string
    {
        return $this->twoStepVerificationCode;
    }

    /**
     * @param string $twoStepVerificationCode
     * @return User
     */
    public function setTwoStepVerificationCode($twoStepVerificationCode): UserInterface
    {
        $this->twoStepVerificationCode = $twoStepVerificationCode;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     * @return User
     */
    public function setLocale($locale): UserInterface
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return Person
     */
    public function getPerson(): Person
    {
        return $this->person;
    }

    /**
     * @param Person $person
     * @return User
     */
    public function setPerson(Person $person): UserInterface
    {
        $this->person = $person;

        if ($person === null && $this->person instanceof Person) {
            $this->removeReferencedEntity($this->person);
        } else {
            $this->addReferencedEntity($person);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setGroups($groups): GroupableInterface
    {
        foreach ($groups as $group) {
            $this->addGroup($group);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setTimezone($timezone): UserInterface
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTimezone(): ?string
    {
        return $this->timezone;
    }
}
