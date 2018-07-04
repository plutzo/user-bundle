<?php

namespace Marlinc\UserBundle\Model;

use Doctrine\Common\Collections\Collection;

/**
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
interface GroupableInterface
{
    /**
     * Gets the groups granted to the user.
     *
     * @return GroupInterface[]|Collection
     */
    public function getGroups(): Collection;

    /**
     * Sets the user groups.
     *
     * @param array $groups
     *
     * @return GroupableInterface
     */
    public function setGroups($groups): GroupableInterface;

    /**
     * Gets the name of the groups which includes the user.
     *
     * @return array
     */
    public function getGroupNames(): array;

    /**
     * Indicates whether the user belongs to the specified group or not.
     *
     * @param string $name Name of the group
     *
     * @return bool
     */
    public function hasGroup($name): bool;

    /**
     * Add a group to the user groups.
     *
     * @param GroupInterface $group
     *
     * @return static
     */
    public function addGroup(GroupInterface $group): GroupableInterface;

    /**
     * Remove a group from the user groups.
     *
     * @param GroupInterface $group
     *
     * @return static
     */
    public function removeGroup(GroupInterface $group): GroupableInterface;
}
