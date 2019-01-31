<?php
/**
 * Created by PhpStorm.
 * User: elias
 * Date: 01.09.17
 * Time: 16:57
 */

namespace Marlinc\UserBundle\Form;

use Marlinc\PostalCodeBundle\Form\Type\PostalCodeSelectType;
use Marlinc\UserBundle\Entity\Person;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonFormType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'Please choose' => '',
                    'Male' => 'm',
                    'Female' => 'f'
                ],
            ])
            ->add('firstname')
            ->add('lastname');

        // Optional fields from here

        if (in_array('thoroughfare', $options['enabled_fields'])) {
            $builder->add('thoroughfare', null, [
                'required' => in_array('thoroughfare', $options['required_fields'])
            ]);
        }

        if (in_array('postalCode', $options['enabled_fields'])) {
            if ($options['country'] === null) {
                $builder->add('postalCode', PostalCodeSelectType::class, [
                    'required' => in_array('postalCode', $options['required_fields']),
                    'attr' => [
                        'data-theme' => 'bootstrap'
                    ],
                    'width' => '100%'
                ]);
            } else {
                $builder->add('postalCode', PostalCodeSelectType::class, [
                    'required' => in_array('postalCode', $options['required_fields']),
                    'remote_route' => 'ajax_postalcode_country',
                    'remote_params' => ['country' => $options['country']],
                    'attr' => [
                        'data-theme' => 'bootstrap'
                    ],
                    'width' => '100%'
                ]);
            }
        }

        if (in_array('phone', $options['enabled_fields'])) {
            $builder->add('phone', PhoneNumberType::class, [
                'default_region' => 'DE',
                'widget' => PhoneNumberType::WIDGET_COUNTRY_CHOICE,
                'country_choices' => ['DE', 'AT', 'CH'],
                'required' => in_array('phone', $options['required_fields']),
            ]);
        }

        if (in_array('mobile', $options['enabled_fields'])) {
            $builder->add('mobile', PhoneNumberType::class, [
                'default_region' => 'DE',
                'widget' => PhoneNumberType::WIDGET_COUNTRY_CHOICE,
                'country_choices' => ['DE', 'AT', 'CH'],
                'required' => in_array('mobile', $options['required_fields']),
            ]);
        }

        if (in_array('email', $options['enabled_fields'])) {
            $builder->add('email', RepeatedType::class, [
                'first_name'  => 'input1',
                'second_name' => 'input2',
                'first_options' => ['label' => 'E-Mail'],
                'second_options' => ['label' => 'Repeat E-Mail'],
                'invalid_message' => 'Die eingegebenen Mail-Adressen in den beiden Feldern stimmen nicht überein.'
            ]);
        }

        if (in_array('newsletter', $options['enabled_fields'])) {
            $builder->add('newsletter', null, [
                'required' => in_array('newsletter', $options['required_fields']),
            ]);
        }

        if (in_array('birthday', $options['enabled_fields'])) {
            $builder->add('birthday', null, [
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd.MM.yyyy',
                'required' => in_array('birthday', $options['required_fields']),
            ]);
        }

        if (in_array('passportNr', $options['enabled_fields'])) {
            $builder->add('passportNr', null, [
                'required' => in_array('passportNr', $options['required_fields'])
                ]
            );
        }

        if (in_array('passportIssueDate', $options['enabled_fields'])) {
            $builder->add('passportIssueDate', DateType::class, [
                    'widget' => 'choice',
                    'html5' => true,
                    'format' => 'dd.MM.yyyy',
                    'required' => in_array('passportIssueDate', $options['required_fields'])
                ]
            );
        }

        if (in_array('passportValidDate', $options['enabled_fields'])) {
            $builder->add('passportValidDate', null, [
                    'widget' => 'choice',
                    'html5' => false,
                    'format' => 'dd.MM.yyyy',
                    'required' => in_array('passportValidDate', $options['required_fields'])
                ]
            );
        }

        if (in_array('driverLicenseNr', $options['enabled_fields'])) {
            $builder->add('driverLicenseNr', null, [
                    'required' => in_array('driverLicenseNr', $options['required_fields'])
                ]
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Person::class,
            'country' => null,
            'enabled_fields' => ['postalCode', 'thoroughfare', 'phone', 'email'],
            'required_fields' => ['postalCode', 'thoroughfare', 'phone', 'email'],
            'validation_groups' => function (FormInterface $form) {
                $groups = [];

                if ($form->getConfig()->hasOption('required_fields') && is_array($form->getConfig()->getOption('required_fields'))) {
                    $groups = $form->getConfig()->getOption('required_fields');
                }

                $groups[] = 'Default';

                return $groups;
            },
        ]);
    }
}