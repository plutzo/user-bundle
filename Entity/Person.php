<?php

namespace Marlinc\UserBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Fresh\DoctrineEnumBundle\Validator\Constraints\Enum as AssertEnum;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use libphonenumber\PhoneNumber;
use Marlinc\PostalCodeBundle\Entity\PostalCodeLocation;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhone;
use Sonata\UserBundle\Model\UserInterface;
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
 * @ORM\Table(name="person")
 * @ORM\Entity
 * @UniqueEntity(
 *     fields={"lastname","firstname","thoroughfare","postalCode"},
 *     message="A person with this data (name and address) already exists in the database.",
 *     groups={"UniquePerson"}
 * )
 */
class Person
{
    use TimestampableEntity;

    /**
     * @var int
     *
     * @Groups({"person_read"})
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

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
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=190, nullable=true)
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="thoroughfare", type="string", length=190, nullable=true)
     * @Assert\NotBlank(groups={"FullAddress"})
     */
    private $thoroughfare;

    /**
     * @var PostalCodeLocation
     *
     * @ORM\ManyToOne(targetEntity="Marlinc\PostalCodeBundle\Entity\PostalCodeLocation")
     * @ORM\JoinColumn(nullable=true)
     * @Assert\NotBlank(groups={"FullAddress"})
     */
    private $postalCode;

    /**
     * @var PhoneNumber
     *
     * @ORM\Column(name="phone", type="phone", nullable=true)
     * @AssertPhone()
     */
    private $phone;

    /**
     * @var PhoneNumber
     *
     * @ORM\Column(name="mobile", type="phone", nullable=true)
     * @AssertPhone()
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
     */
    private $newsletter = false;

    /**
     * @var string
     *
     * @ORM\Column(name="newsletter_token", type="string", length=50, nullable=true)
     */
    private $newsletterToken;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthday;

    /**
     * Person constructor.
     */
    public function __construct()
    {
        $this->gender = UserInterface::GENDER_UNKNOWN;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
    public function setPhone($phone)
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
    public function setMobile($mobile)
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
     * @param \DateTime $birthday
     * @return Person
     */
    public function setBirthday($birthday): Person
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

    function __toString()
    {
        return $this->getFirstname().' '.$this->getLastname();
    }

}

