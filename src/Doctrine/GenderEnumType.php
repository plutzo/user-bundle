<?php
/**
 * Created by PhpStorm.
 * User: elias
 * Date: 12.08.16
 * Time: 17:17
 */

namespace Marlinc\UserBundle\Doctrine;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class GenderEnumType extends AbstractEnumType
{
    protected $name = 'enumgender';

    const GENDER_FEMALE = 'f';
    const GENDER_MALE = 'm';
    const GENDER_UNKNOWN = 'u';

    protected static $choices = [
        self::GENDER_UNKNOWN => 'Unknown',
        self::GENDER_FEMALE => 'Female',
        self::GENDER_MALE => 'Male'
    ];
}