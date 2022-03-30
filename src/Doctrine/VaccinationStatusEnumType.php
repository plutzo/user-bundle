<?php
/**
 * Created by PhpStorm.
 * User: elias
 * Date: 12.08.16
 * Time: 17:17
 */

namespace Marlinc\UserBundle\Doctrine;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class VaccinationStatusEnumType extends AbstractEnumType
{
    protected $name = 'enumvaccination';

    const VACCINATION_COMPLETE = 'c';
    const VACCINATION_RECOVERED = 'r';
    const VACCINATION_BOOSTERED  = 'b';
    const VACCINATION_NONE  = 'n';

    protected static $choices = [
        self::VACCINATION_COMPLETE => 'vaccinated',
        self::VACCINATION_RECOVERED => 'recovered',
        self::VACCINATION_BOOSTERED => 'boostered',
        self::VACCINATION_NONE => 'none',
    ];
}