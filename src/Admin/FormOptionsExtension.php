<?php

namespace Marlinc\UserBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;

class FormOptionsExtension extends AbstractAdminExtension
{
    public function configureFormOptions(AdminInterface $admin, array &$formOptions): void
    {
        $formOptions['validation_groups'] = (!$admin->getSubject() || null === $admin->getSubject()->getId()) ? 'Registration' : 'Profile';
    }
}