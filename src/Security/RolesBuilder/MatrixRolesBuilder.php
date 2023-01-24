<?php

declare(strict_types=1);


namespace Marlinc\UserBundle\Security\RolesBuilder;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @author Silas Joisten <silasjoisten@hotmail.de>
 */
final class MatrixRolesBuilder implements MatrixRolesBuilderInterface
{
    private TokenStorageInterface $tokenStorage;

    private AdminRolesBuilderInterface $adminRolesBuilder;

    private ExpandableRolesBuilderInterface $securityRolesBuilder;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        AdminRolesBuilderInterface $adminRolesBuilder,
        ExpandableRolesBuilderInterface $securityRolesBuilder
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->adminRolesBuilder = $adminRolesBuilder;
        $this->securityRolesBuilder = $securityRolesBuilder;
    }

    public function getRoles(?string $domain = null): array
    {
        if (null === $this->tokenStorage->getToken()) {
            return [];
        }

        return array_merge(
            $this->securityRolesBuilder->getRoles($domain),
            $this->adminRolesBuilder->getRoles($domain)
        );
    }

    public function getExpandedRoles(?string $domain = null): array
    {
        if (null === $this->tokenStorage->getToken()) {
            return [];
        }

        return array_merge(
            $this->securityRolesBuilder->getExpandedRoles($domain),
            $this->adminRolesBuilder->getRoles($domain)
        );
    }

    public function getPermissionLabels(): array
    {
        return $this->adminRolesBuilder->getPermissionLabels();
    }
}
