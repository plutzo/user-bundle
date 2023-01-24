<?php

declare(strict_types=1);


namespace Marlinc\UserBundle\Security\RolesBuilder;

/**
 * @author Silas Joisten <silasjoisten@hotmail.de>
 */
interface PermissionLabelsBuilderInterface
{
    /**
     * @return array<string, string>
     */
    public function getPermissionLabels(): array;
}
