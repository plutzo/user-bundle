<?php

declare(strict_types=1);

namespace Marlinc\UserBundle\Security\RolesBuilder;

/**
 * @author Silas Joisten <silasjoisten@hotmail.de>
 */
interface RolesBuilderInterface
{
    public function getRoles(?string $domain = null): array;
}
