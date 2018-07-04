<?php

namespace Marlinc\UserBundle\Util;

class Canonicalizer implements CanonicalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function canonicalize(string $string): string
    {
        $encoding = mb_detect_encoding($string);
        $result = $encoding
            ? mb_convert_case($string, MB_CASE_LOWER, $encoding)
            : mb_convert_case($string, MB_CASE_LOWER);

        return $result;
    }
}
