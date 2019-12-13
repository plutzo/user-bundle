<?php

namespace Marlinc\UserBundle\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Fresh\DoctrineEnumBundle\Validator\Constraints\Enum as AssertEnum;
use libphonenumber\PhoneNumber;
use Marlinc\EntityBundle\Entity\EntityReference;
use Marlinc\PostalCodeBundle\Entity\PostalCodeLocation;
use Marlinc\UserBundle\Doctrine\GenderEnumType;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhone;
use Symfony\Component\Form\Exception\BadMethodCallException;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Person
 *
 * @ApiResource(
 *     collectionOperations={"get"={"method"="GET"}},
 *     itemOperations={
 *          "get"={"method"="GET"},
 *          "put"={"method"="PUT"}
 *     },
 *     attributes={
 *          "normalization_context"={"groups"={"person_read"}},
 *          "denormalization_context"={"groups"={"person_write"}}
 *     })
 * @ORM\Table(name="user_persons")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(
 *     fields={"lastname","firstname","thoroughfare","postalCode"},
 *     message="A person with this data (name and address) already exists in the database.",
 *     groups={"UniquePerson"}
 * )
 */
class Person extends EntityReference
{
    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="Marlinc\UserBundle\Entity\User",mappedBy="person")
     */
    private $user;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $formal;

    /**
     * @var string
     *
     * @Groups({"person_read", "person_write"})
     * @ORM\Column(type="enumgender")
     * @Assert\NotBlank()
     * @AssertEnum(entity="Marlinc\UserBundle\Doctrine\GenderEnumType")
     */
    private $gender;

    /**
     * @var string
     *
     * @Groups({"person_read", "person_write"})
     * @ORM\Column(type="string", length=190)
     * @Assert\NotBlank()
     */
    private $firstname;

    /**
     * @var string
     *
     * @Groups({"person_read", "person_write"})
     * @ORM\Column(type="string", length=190)
     * @Assert\NotBlank()
     */
    private $lastname;

    /**
     * @var string|null
     *
     * @Groups({"person_read", "person_write"})
     * @ORM\Column(name="title", type="string", length=190, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @Groups({"person_read", "person_write"})
     * @ORM\Column(name="email", type="string", length=190, nullable=true)
     * @Assert\Email()
     * @Assert\NotBlank(groups={"email"})
     */
    private $email;

    /**
     * @var string
     *
     * @Groups({"person_read", "person_write"})
     * @ORM\Column(name="thoroughfare", type="string", length=190, nullable=true)
     * @Assert\NotBlank(groups={"FullAddress","thoroughfare"})
     */
    private $thoroughfare;

    /**
     * @var PostalCodeLocation
     *
     * @Groups({"person_read", "person_write"})
     * @ORM\ManyToOne(targetEntity="Marlinc\PostalCodeBundle\Entity\PostalCodeLocation")
     * @ORM\JoinColumn(nullable=true)
     * @Assert\NotBlank(groups={"FullAddress","postalCode"})
     */
    private $postalCode;

    /**
     * @var PhoneNumber
     *
     * @ApiProperty(writableLink=true)
     * @Groups({"person_read", "person_write"})
     * @ORM\Column(name="phone", type="phone", nullable=true)
     * @AssertPhone()
     * @Assert\NotBlank(groups={"phone"})
     */
    private $phone;

    /**
     * @var PhoneNumber
     *
     * @ApiProperty(writableLink=true)
     * @Groups({"person_read", "person_write"})
     * @ORM\Column(name="mobile", type="phone", nullable=true)
     * @AssertPhone()
     * @Assert\NotBlank(groups={"mobile"})
     */
    private $mobile;

    /**
     * @var string
     *
     * @ORM\Column(type="enumclientcrm", nullable=true)
     * @AssertEnum(entity="Marlinc\UserBundle\Doctrine\CrmEnumType")
     */
    private $crmChannel;

    /**
     * @var bool
     *
     * @ORM\Column(name="newsletter", type="boolean")
     * @Assert\NotNull(groups={"newsletter"})
     */
    private $newsletter;

    /**
     * @var string
     *
     * @ORM\Column(name="newsletter_token", type="string", length=50, nullable=true)
     */
    private $newsletterToken;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="date", nullable=true)
     * @Assert\NotNull(groups={"birthday"})
     */
    private $birthday;

    /**
     * @var string|null
     *
     * @Groups({"person_read", "person_write"})
     * @ORM\Column(name="passportNr", type="string", length=190, nullable=true)
     */
    private $passportNr;

    /**
     * @var \DateTime|null
     *
     * @Groups({"person_read", "person_write"})
     * @ORM\Column(name="passportIssueDate", type="date", nullable=true)
     */
    private $passportIssueDate;


    /**
     * @var \DateTime|null
     *
     * @Groups({"person_read", "person_write"})
     * @ORM\Column(name="passportValidDate", type="date", nullable=true)
     */
    private $passportValidDate;

    /**
     * @var string|null
     *
     * @Groups({"person_read", "person_write"})
     * @ORM\Column(name="nationality", type="string", length=190, nullable=true)
     */
    private $nationality;

    /**
     * @var string|null
     *
     * @Groups({"person_read", "person_write"})
     * @ORM\Column(name="driverLicenseNr", type="string", length=190, nullable=true)
     */
    private $driverLicenseNr;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $driverLicenseValid;

    /**
     * @var string|null
     *
     * @Groups({"person_read", "person_write"})
     * @ORM\Column(name="company", type="string", length=190, nullable=true)
     */
    private $company;

    /**
     * @var string|null
     *
     * @Groups({"person_read", "person_write"})
     * @ORM\Column(name="customerId", type="string", length=190, nullable=true)
     */
    private $customerId;


    /**
     * Person constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->gender = GenderEnumType::GENDER_UNKNOWN;
        $this->formal = true;
        $this->newsletter = false;
    }

    public function __toString()
    {
        return $this->getFullName();
    }

    public function getFullName()
    {
        return $this->getFirstname().' '.$this->getLastname();
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function setEntityLabel() {
        $this->setLabel($this->firstname.' '.$this->lastname);
    }

    /**
     * @return string
     */
    public function getEntityLabel(): string
    {
        return $this->__toString();
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return Person
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set givenName
     *
     * @param string $firstname
     *
     * @return Person
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get givenName
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastName
     *
     * @param string $lastname
     *
     * @return Person
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set thoroughfare
     *
     * @param string $thoroughfare
     *
     * @return Person
     */
    public function setThoroughfare($thoroughfare)
    {
        $this->thoroughfare = $thoroughfare;

        return $this;
    }

    /**
     * Get thoroughfare
     *
     * @return string
     */
    public function getThoroughfare()
    {
        return $this->thoroughfare;
    }

    /**
     * Set postalCode
     *
     * @param string $postalCode
     *
     * @return Person
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get postalCode
     *
     * @return PostalCodeLocation
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set phone
     *
     * @param PhoneNumber $phone
     *
     * @return Person
     */
    public function setPhone(PhoneNumber $phone = null)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return PhoneNumber
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set mobile
     *
     * @param PhoneNumber $mobile
     *
     * @return Person
     */
    public function setMobile(PhoneNumber $mobile = null)
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * Get mobile
     *
     * @return PhoneNumber
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Person
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return bool
     */
    public function isNewsletter(): bool
    {
        return $this->newsletter;
    }

    /**
     * @param bool $newsletter
     * @return Person
     */
    public function setNewsletter(bool $newsletter): Person
    {
        $this->newsletter = $newsletter;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param \DateTime|null $birthday
     * @return Person
     */
    public function setBirthday(?\DateTime $birthday): Person
    {
        $this->birthday = $birthday;
        return $this;
    }

    /**
     * @return string
     */
    public function getNewsletterToken()
    {
        return $this->newsletterToken;
    }

    /**
     * @param string $newsletterToken
     * @return Person
     */
    public function setNewsletterToken($newsletterToken): Person
    {
        $this->newsletterToken = $newsletterToken;
        return $this;
    }

    /**
     * @return string
     */
    public function getCrmChannel()
    {
        return $this->crmChannel;
    }

    /**
     * @param string $crmChannel
     * @return Person
     */
    public function setCrmChannel($crmChannel): Person
    {
        $this->crmChannel = $crmChannel;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassportNr(): ?string
    {
        return $this->passportNr;
    }

    /**
     * @param string|null $passportNr
     */
    public function setPassportNr(?string $passportNr)
    {
        $this->passportNr = $passportNr;
    }

    /**
     * @return \DateTime
     */
    public function getPassportIssueDate(): ?\DateTime
    {
        return $this->passportIssueDate;
    }

    /**
     * @param \DateTime|null $passportIssueDate
     */
    public function setPassportIssueDate(?\DateTime $passportIssueDate)
    {
        $this->passportIssueDate = $passportIssueDate;
    }

    /**
     * @return \DateTime
     */
    public function getPassportValidDate(): ?\DateTime
    {
        return $this->passportValidDate;
    }

    /**
     * @param \DateTime|null $passportValidDate
     */
    public function setPassportValidDate(?\DateTime $passportValidDate)
    {
        $this->passportValidDate = $passportValidDate;
    }

    /**
     * @return string
     */
    public function getNationality(): ?string
    {
        return $this->nationality;
    }

    /**
     * @param string|null $nationality
     */
    public function setNationality(?string $nationality): void
    {
        $this->nationality = $nationality;
    }

    /**
     * @return string
     */
    public function getDriverLicenseNr(): ?string
    {
        return $this->driverLicenseNr;
    }

    /**
     * @param string $driverLicenseNr|null
     */
    public function setDriverLicenseNr(?string $driverLicenseNr)
    {
        $this->driverLicenseNr = $driverLicenseNr;
    }

    /**
     * @return bool
     */
    public function isDriverLicenseValid()
    {
        return $this->driverLicenseValid;
    }

    /**
     * @param bool $driverLicenseValid
     */
    public function setDriverLicenseValid(bool $driverLicenseValid): void
    {
        $this->driverLicenseValid = $driverLicenseValid;
    }

    /**
     * @return string|null
     */
    public function getCompany(): ?string
    {
        return $this->company;
    }

    /**
     * @param string|null $company
     */
    public function setCompany(?string $company): void
    {
        $this->company = $company;
    }

    /**
     * @return string|null
     */
    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    /**
     * @param string|null $customerId
     */
    public function setCustomerId(?string $customerId): void
    {
        $this->customerId = $customerId;
    }

    /**
     * @return bool
     */
    public function isFormal(): bool
    {
        return $this->formal;
    }

    /**
     * @param bool $formal
     * @return Person
     */
    public function setFormal($formal): Person
    {
        $this->formal = $formal;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return Person
     */
    public function setUser(User $user = null): Person
    {
        if ($user instanceof User) {
            $user->setPerson($this);
        } elseif ($this->user instanceof User) {
            throw new BadMethodCallException("User can not be unset, once associated.");
        }

        $this->user = $user;

        return $this;
    }
}