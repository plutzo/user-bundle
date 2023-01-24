<?php

declare(strict_types=1);


namespace Marlinc\UserBundle\Security\RolesBuilder;

/**
 * @author Silas Joisten <silasjoisten@hotmail.de>
 *
 * @phpstan-type Role = array{
 *     role: string,
 *     role_translated: string,
 *     is_granted: boolean,
 *     label?: string,
 *     admin_label?: string
 * }
 */
interface RolesBuilderInterface
{
    /**
     * @return array<string, array<string, string|bool>>
     *
     * @phpstan-return array<string, Role>
     */
    public function getRoles(?string $domain = null): array;
}
