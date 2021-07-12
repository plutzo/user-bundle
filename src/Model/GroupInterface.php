<?php

namespace Marlinc\UserBundle\Model;

/**
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
interface GroupInterface
{
    /**
     * @return int
     */
    public function getId(): ?int;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     *
     * @return static
     */
    public function setName($name): GroupInterface;

    /**
     * @return array
     */
    public function getRoles(): array;

    /**
     * @param array $roles
     *
     * @return static
     */
    public function setRoles(array $roles): GroupInterface;

    /**
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role): bool;

    /**
     * @param string $role
     *
     * @return static
     */
    public function addRole($role): GroupInterface;

    /**
     * @param string $role
     *
     * @return static
     */
    public function removeRole($role): GroupInterface;
}
