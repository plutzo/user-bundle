<?php

declare(strict_types=1);


namespace Marlinc\UserBundle\Tests\Resources;

final class XliffTest extends XliffValidatorTestCase
{
    public function getXliffPaths(): iterable
    {
        return [[__DIR__.'/../../Resources/translations']];
    }
}
