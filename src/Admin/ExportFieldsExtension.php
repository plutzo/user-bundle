<?php


namespace Marlinc\UserBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;

class ExportFieldsExtension extends AbstractAdminExtension
{
    public function configureExportFields(AdminInterface $admin, array $fields): array
    {
        return array_filter($admin->getExportFields(), function ($v) {
            return !in_array($v, ['plainPassword', 'password', 'salt']);
        });
    }
}