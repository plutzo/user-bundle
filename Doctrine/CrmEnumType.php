<?php
/**
 * Created by PhpStorm.
 * User: elias
 * Date: 21.11.17
 * Time: 10:30
 */

namespace Marlinc\UserBundle\Doctrine;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class CrmEnumType extends AbstractEnumType
{
    protected $name = 'enumclientcrm';

    const CRM_POST = 'post';
    const CRM_MOBILE = 'mobile';
    const CRM_PHONE = 'phone';
    const CRM_EMAIL = 'email';
    const CRM_NONE = 'none';

    protected static $choices = [
        self::CRM_NONE => '-- None --',
        self::CRM_EMAIL => 'E-Mail',
        self::CRM_MOBILE => 'WhatsApp',
        self::CRM_PHONE => 'Telefon',
        self::CRM_POST => 'Post'
    ];
}