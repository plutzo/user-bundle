<?php

declare(strict_types=1);


namespace Marlinc\UserBundle\Security\RolesBuilder;

/**
 * @author Silas Joisten <silasjoisten@hotmail.de>
 *
 * @phpstan-import-type Role from RolesBuilderInterface
 */
interface ExpandableRolesBuilderInterface extends RolesBuilderInterface
{
    /**
     * @return array<string, array<string, string|bool>>
     *
     * @phpstan-return array<string, Role>
     */
    public function getExpandedRoles(?string $domain = null): array;
}
