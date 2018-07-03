<?php

declare(strict_types=1);

namespace Marlinc\UserBundle\Model;

use FOS\UserBundle\Model\UserInterface as BaseUserInterface;
use Marlinc\UserBundle\Entity\Person;

interface UserInterface extends BaseUserInterface
{
    /**
     * Sets the creation date.
     *
     * @param \DateTime|null $createdAt
     *
     * @return UserInterface
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * Returns the creation date.
     *
     * @return \DateTime|null
     */
    public function getCreatedAt();

    /**
     * Sets the last update date.
     *
     * @param \DateTime|null $updatedAt
     *
     * @return UserInterface
     */
    public function setUpdatedAt(\DateTime $updatedAt);

    /**
     * Returns the last update date.
     *
     * @return \DateTime|null
     */
    public function getUpdatedAt();

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
     * @return string
     */
    public function getTwoStepVerificationCode(): string;

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
     * @return string
     */
    public function getLocale(): string;

    /**
     * @param string $timezone
     *
     * @return UserInterface
     */
    public function setTimezone($timezone): UserInterface;

    /**
     * @return string
     */
    public function getTimezone(): string;

    /**
     * Sets the user groups.
     *
     * @param array $groups
     *
     * @return UserInterface
     */
    public function setGroups($groups): UserInterface;
}
