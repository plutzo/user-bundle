<?php

namespace Marlinc\UserBundle\Validator;

use Marlinc\UserBundle\Model\UserInterface;
use Marlinc\UserBundle\Util\CanonicalizerInterface;
use Symfony\Component\Validator\ObjectInitializerInterface;

/**
 * Automatically updates the canonical fields before validation.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class UserInitializer implements ObjectInitializerInterface
{
    /**
     * @var CanonicalizerInterface
     */
    private $canonicalizer;

    public function __construct(CanonicalizerInterface $canonicalizer)
    {
        $this->canonicalizer = $canonicalizer;
    }

    /**
     * @param object $object
     */
    public function initialize($object)
    {
        if ($object instanceof UserInterface) {
            $object->setEmail($this->canonicalizer->canonicalize($object->getEmail()));
        }
    }
}
