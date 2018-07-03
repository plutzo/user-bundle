<?php

declare(strict_types=1);

namespace Marlinc\UserBundle\Form\Transformer;

use Marlinc\UserBundle\Security\RolesBuilder\EditableRolesBuilder;
use Symfony\Component\Form\DataTransformerInterface;

class RestoreRolesTransformer implements DataTransformerInterface
{
    /**
     * @var array|null
     */
    protected $originalRoles = null;

    /**
     * @var EditableRolesBuilder|null
     */
    protected $rolesBuilder = null;

    /**
     * @param EditableRolesBuilder $rolesBuilder
     */
    public function __construct(EditableRolesBuilder $rolesBuilder)
    {
        $this->rolesBuilder = $rolesBuilder;
    }

    /**
     * @param array|null $originalRoles
     */
    public function setOriginalRoles(array $originalRoles = null): void
    {
        $this->originalRoles = $originalRoles ?: [];
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return $value;
        }

        if (null === $this->originalRoles) {
            throw new \RuntimeException('Invalid state, originalRoles array is not set');
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($selectedRoles)
    {
        if (null === $this->originalRoles) {
            throw new \RuntimeException('Invalid state, originalRoles array is not set');
        }

        $availableRoles = $this->rolesBuilder->getRoles();

        $hiddenRoles = array_diff($this->originalRoles, array_keys($availableRoles));

        return array_merge($selectedRoles, $hiddenRoles);
    }
}
