<?php

namespace Marlinc\UserBundle\Form\Factory;

use Symfony\Component\Form\FormInterface;

interface FactoryInterface
{
    /**
     * @return FormInterface
     */
    public function createForm();
}
