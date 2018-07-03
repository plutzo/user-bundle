<?php

declare(strict_types=1);

namespace Marlinc\UserBundle\Security\RolesBuilder;

/**
 * @author Silas Joisten <silasjoisten@hotmail.de>
 */
interface ExpandableRolesBuilderInterface extends RolesBuilderInterface
{
    public function getExpandedRoles(?string $domain = null): array;
}
