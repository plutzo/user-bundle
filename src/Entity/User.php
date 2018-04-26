<?php

namespace Marlinc\UserBundle\Entity;

use Marlinc\UserBundle\Doctrine\GenderEnumType;
use Marlinc\UserBundle\Traits\BlameableEntity;
use Marlinc\ClientBundle\Entity\Client;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\GroupableInterface;
use FOS\UserBundle\Model\GroupInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Sonata\UserBundle\Model\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class User
 *
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @Gedmo\Loggable
 * @UniqueEntity("email")
 */
class User implements UserInterface, GroupableInterface
{
    use SoftDeleteableEntity;
    use BlameableEntity;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

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
     * @var string
     *
     * @ORM\Column(type="string", length=190, nullable=true)
     */
    protected $twoStepVerificationCode;

    /**
     * Random string sent to the user email address in order to verify it.
     * @var string
     *
     * @ORM\Column(type="string", length=190, nullable=true, unique=true)
     */
    protected $confirmationToken;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Marlinc\UserBundle\Entity\Group")
     * @ORM\JoinTable(name="users_groups",
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
     * TODO: Migrate/remove. Keep getter/setter for symfony/localization compatibility.
     * @ORM\Column(type="string", length=8, nullable=true)
     * @Assert\Locale()
     * @Gedmo\Versioned
     */
    protected $locale;

    /**
     * @var \DateTime
     *
     * TODO: Migrate/remove
     * @ORM\Column(type="date", nullable=true)
     * @Gedmo\Versioned
     */
    protected $dateOfBirth;

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

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    protected $updatedAt;

    public function __construct()
    {
        $this->enabled = false;
        $this->roles = array();
        $this->person = new Person();
    }

    public function __toString()
    {
        return $this->getFullName().' ('.$this->email.')';
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
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
    public function addRole($role)
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
    public function serialize()
    {
        return serialize(array(
            $this->password,
            $this->enabled,
            $this->id,
            $this->email,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        list(
            $this->password,
            $this->enabled,
            $this->id,
            $this->email
            ) = $data;
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
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsernameCanonical()
    {
        return $this->email;
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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailCanonical()
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
    public function getPlainPassword()
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
    public function getConfirmationToken()
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
    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function isSuperAdmin()
    {
        return $this->hasRole(static::ROLE_SUPER_ADMIN);
    }

    /**
     * {@inheritdoc}
     */
    public function removeRole($role)
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
    public function setUsername($username)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsernameCanonical($usernameCanonical)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSalt($salt)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail($email)
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
    public function setEmailCanonical($emailCanonical)
    {
        $this->setEmail($emailCanonical);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled($boolean)
    {
        $this->enabled = (bool) $boolean;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSuperAdmin($boolean)
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
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastLogin(\DateTime $time = null)
    {
        $this->lastLogin = $time;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfirmationToken($confirmationToken)
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
    public function isPasswordRequestNonExpired($ttl)
    {
        return $this->getPasswordRequestedAt() instanceof \DateTime &&
            $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }

    /**
     * {@inheritdoc}
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroups()
    {
        return $this->groups ?: $this->groups = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupNames()
    {
        $names = array();
        foreach ($this->getGroups() as $group) {
            $names[] = $group->getName();
        }

        return $names;
    }

    /**
     * {@inheritdoc}
     */
    public function hasGroup($name)
    {
        return in_array($name, $this->getGroupNames());
    }

    /**
     * {@inheritdoc}
     */
    public function addGroup(GroupInterface $group)
    {
        if (!$this->getGroups()->contains($group)) {
            $this->getGroups()->add($group);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeGroup(GroupInterface $group)
    {
        if ($this->getGroups()->contains($group)) {
            $this->getGroups()->removeElement($group);
        }

        return $this;
    }

    /**
     * Returns the gender list.
     *
     * @return array
     */
    public static function getGenderList()
    {
        return GenderEnumType::getChoices();
    }

    public function getFullName()
    {
        return $this->person->getFirstname().' '.$this->person->getLastname();
    }

    /**
     * @return string
     */
    public function getTwoStepVerificationCode()
    {
        return $this->twoStepVerificationCode;
    }

    /**
     * @param string $twoStepVerificationCode
     * @return User
     */
    public function setTwoStepVerificationCode($twoStepVerificationCode)
    {
        $this->twoStepVerificationCode = $twoStepVerificationCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     * @return User
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * @param \DateTime $dateOfBirth
     * @return User
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;
        return $this;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @param Person $person
     * @return User
     */
    public function setPerson(Person $person): User
    {
        $this->person = $person;
        return $this;
    }

    /**
     * Sets createdAt.
     *
     * @param  \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Returns createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Sets updatedAt.
     *
     * @param  \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Returns updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @inheritDoc
     */
    public function setGroups($groups)
    {
        foreach ($groups as $group) {
            $this->addGroup($group);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setBiography($biography)
    {
        // TODO: Implement setBiography() method.
    }

    /**
     * @inheritDoc
     */
    public function getBiography()
    {
        // TODO: Implement getBiography() method.
    }

    /**
     * @inheritDoc
     */
    public function setFacebookData($facebookData)
    {
        // TODO: Implement setFacebookData() method.
    }

    /**
     * @inheritDoc
     */
    public function getFacebookData()
    {
        // TODO: Implement getFacebookData() method.
    }

    /**
     * @inheritDoc
     */
    public function setFacebookName($facebookName)
    {
        // TODO: Implement setFacebookName() method.
    }

    /**
     * @inheritDoc
     */
    public function getFacebookName()
    {
        // TODO: Implement getFacebookName() method.
    }

    /**
     * @inheritDoc
     */
    public function setFacebookUid($facebookUid)
    {
        // TODO: Implement setFacebookUid() method.
    }

    /**
     * @inheritDoc
     */
    public function getFacebookUid()
    {
        // TODO: Implement getFacebookUid() method.
    }

    /**
     * @inheritDoc
     */
    public function setFirstname($firstname)
    {
        // TODO: Implement setFirstname() method.
    }

    /**
     * @inheritDoc
     */
    public function getFirstname()
    {
        // TODO: Implement getFirstname() method.
    }

    /**
     * @inheritDoc
     */
    public function setGender($gender)
    {
        // TODO: Implement setGender() method.
    }

    /**
     * @inheritDoc
     */
    public function getGender()
    {
        // TODO: Implement getGender() method.
    }

    /**
     * @inheritDoc
     */
    public function setGplusData($gplusData)
    {
        // TODO: Implement setGplusData() method.
    }

    /**
     * @inheritDoc
     */
    public function getGplusData()
    {
        // TODO: Implement getGplusData() method.
    }

    /**
     * @inheritDoc
     */
    public function setGplusName($gplusName)
    {
        // TODO: Implement setGplusName() method.
    }

    /**
     * @inheritDoc
     */
    public function getGplusName()
    {
        // TODO: Implement getGplusName() method.
    }

    /**
     * @inheritDoc
     */
    public function setGplusUid($gplusUid)
    {
        // TODO: Implement setGplusUid() method.
    }

    /**
     * @inheritDoc
     */
    public function getGplusUid()
    {
        // TODO: Implement getGplusUid() method.
    }

    /**
     * @inheritDoc
     */
    public function setLastname($lastname)
    {
        // TODO: Implement setLastname() method.
    }

    /**
     * @inheritDoc
     */
    public function getLastname()
    {
        // TODO: Implement getLastname() method.
    }

    /**
     * @inheritDoc
     */
    public function setPhone($phone)
    {
        // TODO: Implement setPhone() method.
    }

    /**
     * @inheritDoc
     */
    public function getPhone()
    {
        // TODO: Implement getPhone() method.
    }

    /**
     * @inheritDoc
     */
    public function setTimezone($timezone)
    {
        // TODO: Implement setTimezone() method.
    }

    /**
     * @inheritDoc
     */
    public function getTimezone()
    {
        // TODO: Implement getTimezone() method.
    }

    /**
     * @inheritDoc
     */
    public function setTwitterData($twitterData)
    {
        // TODO: Implement setTwitterData() method.
    }

    /**
     * @inheritDoc
     */
    public function getTwitterData()
    {
        // TODO: Implement getTwitterData() method.
    }

    /**
     * @inheritDoc
     */
    public function setTwitterName($twitterName)
    {
        // TODO: Implement setTwitterName() method.
    }

    /**
     * @inheritDoc
     */
    public function getTwitterName()
    {
        // TODO: Implement getTwitterName() method.
    }

    /**
     * @inheritDoc
     */
    public function setTwitterUid($twitterUid)
    {
        // TODO: Implement setTwitterUid() method.
    }

    /**
     * @inheritDoc
     */
    public function getTwitterUid()
    {
        // TODO: Implement getTwitterUid() method.
    }

    /**
     * @inheritDoc
     */
    public function setWebsite($website)
    {
        // TODO: Implement setWebsite() method.
    }

    /**
     * @inheritDoc
     */
    public function getWebsite()
    {
        // TODO: Implement getWebsite() method.
    }

    /**
     * @inheritDoc
     */
    public function setToken($token)
    {
        // TODO: Implement setToken() method.
    }

    /**
     * @inheritDoc
     */
    public function getToken()
    {
        // TODO: Implement getToken() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getRealRoles()
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function setRealRoles(array $roles)
    {
        $this->setRoles($roles);

        return $this;
    }
}
