<?php

declare(strict_types=1);

namespace Marlinc\UserBundle\Model;

use Sonata\Doctrine\Model\PageableManagerInterface;

/**
 * @author Hugo Briand <briand@ekino.com>
 */
interface GroupManagerInterface extends PageableManagerInterface
{
    /**
     * Returns the group's fully qualified class name.
     *
     * @return string
     */
    public function getClass(): string;

    /**
     * Returns an empty group instance.
     *
     * @param string $name
     *
     * @return GroupInterface
     */
    public function createGroup($name): GroupInterface;

    /**
     * Updates a group.
     *
     * @param GroupInterface $group
     * @return GroupManagerInterface
     */
    public function updateGroup(GroupInterface $group): GroupManagerInterface;

    /**
     * Deletes a group.
     *
     * @param GroupInterface $group
     *
     * @return GroupManagerInterface
     */
    public function deleteGroup(GroupInterface $group): GroupManagerInterface;

    /**
     * Finds one group by the given criteria.
     *
     * @param array $criteria
     *
     * @return GroupInterface
     */
    public function findGroupBy(array $criteria): ?GroupInterface;

    /**
     * Finds a group by name.
     *
     * @param string $name
     *
     * @return GroupInterface
     */
    public function findGroupByName($name): ?GroupInterface;

    /**
     * Returns a collection with all group instances.
     *
     * @return array
     */
    public function findGroups(): array;

    /**
     * Alias for the repository method.
     *
     * @param array|null $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return GroupInterface[]
     */
    public function findGroupsBy(array $criteria = null, array $orderBy = null, $limit = null, $offset = null): array;
}
