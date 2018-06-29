<?php
/**
 * Created by PhpStorm.
 * User: em
 * Date: 29.06.18
 * Time: 13:35
 */

namespace Marlinc\UserBundle\Validator;


use Symfony\Component\Form\FormInterface;

class PersonValidationGroupResolver
{
    /**
     * @param FormInterface $form
     * @return array
     */
    public function __invoke(FormInterface $form)
    {
        $groups = [];

        if ($form->getConfig()->hasOption('required_fields') && is_array($form->getConfig()->getOption('required_fields'))) {
            $groups = $form->getConfig()->getOption('required_fields');
        }

        return $groups;
    }
}