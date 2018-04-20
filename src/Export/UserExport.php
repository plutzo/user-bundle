<?php
/**
 * Created by PhpStorm.
 * User: elias
 * Date: 17.10.17
 * Time: 16:56
 */

namespace Marlinc\UserBundle\Export;


use Marlinc\AdminBundle\Export\ExportColumn;
use Marlinc\AdminBundle\Export\ExportFormat;

class UserExport extends ExportFormat
{
    /**
     * Export constructor.
     */
    public function __construct()
    {
        $this->addColumn(
            'E-Mail',
            ExportColumn::TYPE_SINGLE,
            null,
            ['email']
        );
        $this->addColumn(
            'Vorname',
            ExportColumn::TYPE_SINGLE,
            null,
            ['person.firstname']
        );
        $this->addColumn(
            'Nachname',
            ExportColumn::TYPE_SINGLE,
            null,
            ['person.lastname']
        );
        $this->addColumn(
            'Firma',
            ExportColumn::TYPE_SINGLE,
            null,
            ['client.name']
        );
        $this->addColumn(
            'Newsletter',
            ExportColumn::TYPE_SINGLE,
            null,
            ['person.newsletter']
        );
    }
}