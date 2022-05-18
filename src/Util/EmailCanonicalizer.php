<?php


namespace Marlinc\UserBundle\Util;

use Marlinc\UserBundle\Entity\UserInterface;

class EmailCanonicalizer
{
    static function canonicalize(?string $string): ?string
    {
        if (null === $string) {
            return null;
        }

        $detectedOrder = mb_detect_order();
        \assert(\is_array($detectedOrder));

        $encoding = mb_detect_encoding($string, $detectedOrder, true);

        return false !== $encoding
            ? mb_convert_case($string, \MB_CASE_LOWER, $encoding)
            : mb_convert_case($string, \MB_CASE_LOWER);
    }
}