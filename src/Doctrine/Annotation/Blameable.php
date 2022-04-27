<?php

namespace Marlinc\UserBundle\Doctrine\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Blameable annotation for Blameable behavioral extension
 *
 * @Annotation
 * @Target("PROPERTY")
 */
final class Blameable extends Annotation
{
    /** @var string */
    public $on = 'delete';
    /** @var string|array */
    public $field;
    /** @var mixed */
    public $value;
}
